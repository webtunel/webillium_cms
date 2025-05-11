<?php namespace webtunel\webilliumcms\commands;

use Illuminate\Console\Command;

class WebilliumQuickInstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'webillium:quick-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quick installation of WebilliumCMS with default settings';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║          WebilliumCMS Quick Installation Wizard          ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        
        $this->info('Checking system requirements...');
        
        // Check PHP version
        $this->info('PHP version: ' . phpversion());
        if (version_compare(phpversion(), '7.3', '<')) {
            $this->error('PHP version must be at least 7.3. Your version is ' . phpversion());
            return;
        }
        
        // Check Laravel version
        $laravel = app();
        $this->info('Laravel version: ' . $laravel::VERSION);
        if (version_compare($laravel::VERSION, '8.0', '<')) {
            $this->error('Laravel version must be at least 8.0. Your version is ' . $laravel::VERSION);
            return;
        }
        
        // Check database configuration
        if (!$this->checkDatabaseConnection()) {
            $this->error('Database connection failed. Please check your .env file.');
            return;
        }
        
        $this->info('All requirements met. Starting installation...');
        
        // Create vendor directory if it doesn't exist
        if (!file_exists(public_path('vendor'))) {
            mkdir(public_path('vendor'), 0777);
        }
        
        // Publish assets
        $this->info('Publishing WebilliumCMS assets...');
        $this->call('vendor:publish', [
            '--provider' => 'webtunel\webilliumcms\CRUDBoosterServiceProvider',
            '--force' => true
        ]);
        
        // Run migrations and seed the database
        $this->info('Running database migrations and seeding...');
        $this->call('migrate');
        $this->call('db:seed', ['--class' => 'CBSeeder']);
        $this->call('config:clear');
        
        $this->info('Creating initial admin user...');
        $this->createAdminUser();
        
        $this->info('═════════════════════════════════════════════════════════════');
        $this->info('✅ WebilliumCMS has been successfully installed!');
        $this->info('   You can now login at: ' . url('/admin'));
        $this->info('   Default login: admin@admin.com / 123456');
        $this->info('═════════════════════════════════════════════════════════════');
        $this->info('📚 Documentation: https://github.com/webtunel/webillium_cms/blob/master/docs/en/index.md');
    }
    
    /**
     * Check if database connection is set up correctly
     */
    private function checkDatabaseConnection()
    {
        try {
            \DB::connection()->getPdo();
            $this->info('✓ Database connection successful: ' . \DB::connection()->getDatabaseName());
            return true;
        } catch (\Exception $e) {
            $this->error('✗ Database connection failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create default admin user if it doesn't exist
     */
    private function createAdminUser()
    {
        // Check if admin user already exists
        $adminExists = \DB::table('cms_users')
            ->where('email', 'admin@admin.com')
            ->exists();
            
        if (!$adminExists) {
            \DB::table('cms_users')->insert([
                'name' => 'Administrator',
                'email' => 'admin@admin.com',
                'password' => bcrypt('123456'),
                'id_cms_privileges' => 1,
                'status' => 'Active',
                'created_at' => now(),
            ]);
            $this->info('Admin user created successfully.');
        } else {
            $this->info('Admin user already exists, skipping...');
        }
    }
}