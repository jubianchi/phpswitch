<?php
namespace jubianchi\PhpSwitch\Console\Command\PHP\Doc;

use Symfony\Component\Console;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use jubianchi\PhpSwitch\Console\Command\Command;

class InstallCommand extends Command
{
    const NAME = 'php:doc:install';
    const DESC = 'Installs PHP offline documentation';

    const URL = 'http://php.net/get/%s/from/%s/mirror';
    const FORMAT_HTML = 'html';
    const FORMAT_SINGLE_HTML = 'single-html';
    const FORMAT_CHM = 'chm';
    const FORMAT_CHM_ENH = 'chm-enhanced';

    private static $formats = array(
        self::FORMAT_HTML => 'php_manual_%s.tar.gz',
        self::FORMAT_SINGLE_HTML => 'php_manual_%s.html.gz',
        self::FORMAT_CHM => 'php_%s.chm',
        self::FORMAT_CHM_ENH => 'php_enhanced_%s.chm',
    );

    private static $outputs = array(
        self::FORMAT_HTML => 'php-chunked-xhtml',
        self::FORMAT_SINGLE_HTML => 'php_manual_fr.html'
    );

    private static $langs = array(
        'en' => 'en',
        'pt' => 'pt_BR',
        'cn' => 'zh',
        'fr' => 'fr',
        'de' => 'de',
        'it' => 'it',
        'ja' => 'ja',
        'pl' => 'pl',
        'ro' => 'ro',
        'ru' => 'ru',
        'es' => 'es',
        'tr' => 'tr',
    );

    /**
     * @param string $name
     */
    public function __construct($name = self::NAME)
    {
        parent::__construct($name);

        $this
            ->addOption(
                'format',
                '-f',
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    'Documentation format <comment>(%s)</comment>',
                    implode(', ', array_keys(static::$formats))
                ),
                self::FORMAT_HTML
            )
            ->addOption(
                'lang',
                'l',
                InputOption::VALUE_OPTIONAL,
                sprintf(
                    'Documentation language <comment>(%s)</comment>',
                    implode(', ', array_keys(static::$langs))
                ),
                'en'
            )
        ;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        parent::execute($input, $output);
        
        $format = $input->getOption('format');
        $lang = $input->getOption('lang');
        $filename = static::getFilename($format, $lang);
        $url = sprintf(self::URL, $filename, $this->getConfiguration()->get('mirror'));
        $archive = $this->getApplication()->getService('app.workspace.doc.path') . DIRECTORY_SEPARATOR . $filename;

        $output->writeln(array(
            sprintf(PHP_EOL . 'Downloading PHP documentation <info>(%s/%s)</info>', $format, $lang),
            sprintf('%s<comment>%s</comment>', self::INDENT, $url)
        ));

        static::download($url, $archive, $this->getDownloadProgressCallback($output));

        if (false === in_array($format, array(static::FORMAT_CHM, self::FORMAT_CHM_ENH))) {
            $destination = $this->getApplication()->getService('app.workspace.doc.path') . DIRECTORY_SEPARATOR . sprintf('php_manual-%s-%s', $format, $lang) . (static::FORMAT_SINGLE_HTML === $format ? '.html' : '');

            $output->writeln(array(
                PHP_EOL . PHP_EOL . 'Extracting PHP documentation',
                sprintf('%s<comment>%s</comment>', self::INDENT, $destination)
            ));

            $process = new Process(
                sprintf(
                    static::FORMAT_HTML === $format ? 'tar -zxvf %s' : 'gunzip %s',
                    $archive
                ),
                dirname($destination)
            );
            $process->run($this->getProcessCallback($output));

            $process = new Process(
                sprintf(
                    'mv %s %s',
                    static::$outputs[$format],
                    $destination
                ),
                dirname($archive)
            );
            $process->run($this->getProcessCallback($output));

            if(file_exists($archive)) {
                unlink($archive);
            }
        } else {
            $destination = $this->getApplication()->getService('app.workspace.doc.path') . DIRECTORY_SEPARATOR . $filename;
        }

        $output->writeln(array(
            PHP_EOL . PHP_EOL . 'PHP offline documentation is now installed',
            sprintf('%s<comment>%s</comment>', self::INDENT, $destination)
        ));
    }

    private static function getFilename($format, $lang)
    {
        if (false === isset(static::$formats[$format])) {
            throw new \InvalidArgumentException(sprintf('Invalid format "%s"', $format));
        }

        if (false === isset(static::$langs[$lang])) {
            throw new \InvalidArgumentException(sprintf('Invalid lang "%s"', $format));
        }

        return sprintf(static::$formats[$format], static::$langs[$lang]);
    }

    private static function download($url, $destination, $callback = null)
    {
        $handle = fopen($destination, 'wb+');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FILE, $handle);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if (null !== $callback) {
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $callback);
        }

        curl_exec($ch);
        curl_close($ch);
        fclose($handle);
    }

    protected function getProcessCallback(OutputInterface $output)
    {
        $self = $this;

        if (OutputInterface::VERBOSITY_VERBOSE !== $output->getVerbosity()) {
            $this->startProgress($output);
        }

        return function($type, $buffer) use ($self, $output) {
            $buffer = rtrim($buffer);
            if ('' === empty($buffer)) {
                return;
            }

            $self->log($buffer, 'err' === $type ? \Monolog\Logger::ERROR : \Monolog\Logger::INFO);
            $self->getHelper('progress')->advance();
        };
    }

    protected function getDownloadProgressCallback(OutputInterface $output)
    {
        $self = $this;

        $this->startProgress($output, 100, '[%bar%] %percent%%');

        return function($download_size, $downloaded_size, $upload_size, $uploaded_size) use($self) {
            static $previous = 0;

            if($download_size > 0) {
                $complete = ceil(($downloaded_size / $download_size) * 100);

                $self->getHelper('progress')->advance($complete - $previous);

                $previous = $complete;
            }
        };
    }

    protected function startProgress(OutputInterface $output, $max = null, $format = '[%bar%]')
    {
        $progress = $this->getHelper('progress');

        $progress->setBarWidth(50);
        $progress->setEmptyBarCharacter($max ? '-' : '=');
        $progress->setProgressCharacter('>');
        $progress->setFormat($format);

        $progress->start($output, $max);
    }
}
