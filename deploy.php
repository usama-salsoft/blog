<?php
namespace Deployer;

require 'recipe/laravel.php';
require 'contrib/npm.php';
require 'contrib/rsync.php';

set('application', 'test for CICD');
set('ssh_multiplexing', true);
set('repository', 'https://github.com/usama-salsoft/blog.git');

set('rsync_src', function () {
    return __DIR__; // If your project isn't in the root, you'll need to change this.
});

add('rsync', [
    'exclude' => [
        '.git',
        '/.env',
        '/storage/',
        '/vendor/',
        '/node_modules/',
        '.github',
        'deploy.php',
    ],
]);

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

task('deploy:secrets', function () {
    file_put_contents(__DIR__ . '/.env', getenv('DOT_ENV'));
    upload('.env', get('deploy_path') . '/shared');
});

// Hosts

// host('custom-dev.onlinetestingserver.com')
//     ->set('hostname','45.63.58.248')
//     ->set('remote_user', 'customdevonline')
//     ->set('deploy_path', '~/www/testForCICD');

host('dev74.onlinetestingserver.com')
    ->set('hostname','169.47.198.147')
    ->set('remote_user', 'dev74')
    ->set('branch', 'main')
    ->set('deploy_path', '~/public_html/testForCICD');

// Hooks

after('deploy:failed', 'deploy:unlock');

desc('================================================================');
desc('Start of Deploy the application');
task('deploy', [
    'deploy:prepare',
    'rsync',                // Deploy code & built assets
    'deploy:secrets',       // Deploy secrets
    'deploy:vendors',
    'deploy:shared',        //
    'artisan:storage:link', //
    'artisan:view:cache',   //
    'artisan:config:cache', // Laravel specific steps
    'artisan:migrate',      //
    'artisan:queue:restart',//
    'deploy:publish',       //
]);
desc('End of Deploy the application');
