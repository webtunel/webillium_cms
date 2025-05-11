<?php namespace webtunel\webilliumcms\commands;

use App;
use Cache;
use CRUDBooster;
use DB;
use Illuminate\Console\Command;
use Request;
use Symfony\Component\Process\Process;

class CrudboosterInstallationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'webillium:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Webillium CMS Installation Command';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->header();

        $this->checkRequirements();

        $this->info('Installing: ');

        if ($this->confirm('Do you have setting the database configuration at .env ?')) {

            if (! file_exists(public_path('vendor'))) {
                mkdir(public_path('vendor'), 0777);
            }

            $this->info('Publishing WebilliumCMS assets...');
            $this->call('vendor:publish', ['--provider' => 'webtunel\webilliumcms\CRUDBoosterServiceProvider']);

            $this->info('Dumping the autoloaded files and reloading all new files...');
            $composer = $this->findComposer();

            $process = (app()->version() >= 7.0)
                ? new Process([$composer.' dumpautoload'])
                : new Process($composer.' dumpautoload');

            $process->setWorkingDirectory(base_path())->run();

            $this->info('Migrating database...');

            $this->call('migrate');
            $this->call('db:seed', ['--class' => 'CBSeeder']);
            $this->call('config:clear');
            if (app()->version() < 5.6) {
                $this->call('optimize');
            }

            $this->info('Installing WebilliumCMS Is Completed ! Thank You :)');
        } else {
            $this->info('Setup Aborted !');
            $this->info('Please setting the database configuration for first !');
        }

        $this->footer();
    }

    private function header()
    {
        $this->info("
# __      __      ___.   .__.__  .__  .__                _________     _____    _________
#/  \    /  \ ____\_ |__ |__|  | |  | |__|__ __  _____   \_   ___ \   /     \  /   _____/
#\   \/\/   // __ \| __ \|  |  | |  | |  |  |  \/     \  /    \  \/  /  \ /  \ \_____  \ 
# \        /\  ___/| \_\ \  |  |_|  |_|  |  |  /  Y Y  \ \     \____/    Y    \/        \
#  \__/\  /  \___  >___  /__|____/____/__|____/|__|_|  /  \______  /\____|__  /_______  /
#       \/       \/    \/                            \/          \/         \/        \/                                                                                                            
			");
        $this->info('--------- :===: Thanks for choosing Webillium CMS :==: ---------------');
        $this->info('====================================================================');
    }

    private function checkRequirements()
    {
        $this->info('System Requirements Checking:');
        $system_failed = 0;
        $laravel = app();

        $this->info("Your laravel version: ".$laravel::VERSION);
        $this->info("---");

        if (version_compare($laravel::VERSION, "6.0", ">=")) {
            $this->info('Laravel Version (>= 6.x): [Good]');
        } else {
            $this->info('Laravel Version (>= 6.x): [Bad]');
            $system_failed++;
        }

        if (version_compare(phpversion(), '7.2.0', '>=')) {
            $this->info('PHP Version (>= 7.2.*): [Good]');
        } else {
            $this->info('PHP Version (>= 7.2.*): [Bad] Yours: '.phpversion());
            $system_failed++;
        }

        if (extension_loaded('mbstring')) {
            $this->info('Mbstring extension: [Good]');
        } else {
            $this->info('Mbstring extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('openssl')) {
            $this->info('OpenSSL extension: [Good]');
        } else {
            $this->info('OpenSSL extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('pdo')) {
            $this->info('PDO extension: [Good]');
        } else {
            $this->info('PDO extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('tokenizer')) {
            $this->info('Tokenizer extension: [Good]');
        } else {
            $this->info('Tokenizer extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('xml')) {
            $this->info('XML extension: [Good]');
        } else {
            $this->info('XML extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('gd')) {
            $this->info('GD extension: [Good]');
        } else {
            $this->info('GD extension: [Bad]');
            $system_failed++;
        }

        if (extension_loaded('fileinfo')) {
            $this->info('PHP Fileinfo extension: [Good]');
        } else {
            $this->info('PHP Fileinfo extension: [Bad]');
            $system_failed++;
        }

        if (is_writable(base_path('public'))) {
            $this->info('/public dir is writable: [Good]');
        } else {
            $this->info('/public dir is writable: [Bad]');
            $system_failed++;
        }

        if ($system_failed != 0) {
            $this->info('Sorry unfortunately your system is not meet with our requirements !');
            $this->footer(false);
        }
        $this->info('--');
    }

    private function footer($success = true)
    {
        $this->info('--');
        $this->info('Github : https://github.com/webtunel/webillium_cms');
        $this->info('Documentation : https://github.com/webtunel/webillium_cms/blob/master/docs/en/index.md');
        $this->info('====================================================================');
        if ($success == true) {
            $this->info('------------------- :===: Completed !! :===: ------------------------');
        } else {
            $this->info('------------------- :===: Failed !!    :===: ------------------------');
        }
        exit;
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }
}
