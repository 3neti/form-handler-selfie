<?php

declare(strict_types=1);

namespace LBHurtado\FormHandlerSelfie\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Install Selfie Handler Command
 * 
 * Installs required UI dependencies and publishes assets.
 */
class InstallSelfieHandlerCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'selfie-handler:install {--force : Overwrite existing files}';

    /**
     * The console command description.
     */
    protected $description = 'Install selfie handler UI dependencies and assets';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Installing Selfie Handler...');
        
        // Check if shadcn-vue is available
        if (!$this->shadcnAvailable()) {
            $this->error('shadcn-vue CLI not found. Please ensure it is installed.');
            $this->line('Run: npm install -D shadcn-vue');
            return self::FAILURE;
        }
        
        // Install required UI components
        $this->installUIComponents();
        
        // Publish Vue components
        $this->publishAssets();
        
        $this->newLine();
        $this->info('✓ Selfie Handler installed successfully!');
        $this->line('  Run "npm run build" to compile frontend assets.');
        
        return self::SUCCESS;
    }
    
    /**
     * Check if shadcn-vue CLI is available
     */
    protected function shadcnAvailable(): bool
    {
        $result = shell_exec('npx shadcn-vue@latest --version 2>&1');
        return $result !== null;
    }
    
    /**
     * Install required UI components
     */
    protected function installUIComponents(): void
    {
        $components = ['alert-dialog'];
        
        foreach ($components as $component) {
            if ($this->componentExists($component) && !$this->option('force')) {
                $this->line("  • {$component} already installed (skipping)");
                continue;
            }
            
            $this->line("  • Installing {$component}...");
            
            $command = "npx shadcn-vue@latest add {$component} --yes --overwrite 2>&1";
            $output = shell_exec($command);
            
            if ($this->componentExists($component)) {
                $this->line("    ✓ {$component} installed");
            } else {
                $this->warn("    ⚠ {$component} installation may have failed");
                if ($output) {
                    $this->line("    Output: {$output}");
                }
            }
        }
    }
    
    /**
     * Check if a component exists
     */
    protected function componentExists(string $component): bool
    {
        $path = resource_path("js/components/ui/{$component}");
        return File::exists($path);
    }
    
    /**
     * Publish package assets
     */
    protected function publishAssets(): void
    {
        $this->line('  • Publishing Vue components...');
        
        $this->call('vendor:publish', [
            '--tag' => 'selfie-handler-stubs',
            '--force' => $this->option('force'),
        ]);
    }
}
