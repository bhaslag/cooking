<?php

/**
 * @file
 * Contains \DrupalProject\composer\ScriptHandler.
 */

namespace DrupalProject\composer;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

class ScriptHandler
{

  protected static $drupal_web_dir = '/src';
  protected static $spbuilder_path = 'scripts/spbuilder';

  protected static $default_dir_list = [
    'modules/custom',
    'profiles',
    'themes',
  ];

  protected static $composer_json_mask_list = ['/sites/*/composer.json','/profiles/*/composer.json'];

  protected static function getDrupalRoot($project_root)
  {
    return $project_root . self::$drupal_web_dir;
  }

  public static function createRequiredFiles(Event $event)
  {
    $fs = new Filesystem();
    $project_root = getcwd();
    $root = static::getDrupalRoot($project_root);

    // Required for unit testing
    foreach (self::$default_dir_list as $dir) {
      if (!$fs->exists($root . '/' . $dir)) {
        $fs->mkdir($root . '/' . $dir);
        $fs->touch($root . '/' . $dir . '/.gitkeep');
      }
    }

    // Prepare the settings file for installation
    if (!$fs->exists($root . '/sites/default/settings.php') and $fs->exists($root . '/sites/default/default.settings.php')) {
      $fs->copy($root . '/sites/default/default.settings.php', $root . '/sites/default/settings.php');
      $fs->chmod($root . '/sites/default/settings.php', 0666);
      $event->getIO()->write('Create a sites/default/settings.php file with chmod 0666');
    }

    // Prepare the services file for installation
    if (!$fs->exists($root . '/sites/default/services.yml') and $fs->exists($root . '/sites/default/default.services.yml')) {
      $fs->copy($root . '/sites/default/default.services.yml', $root . '/sites/default/services.yml');
      $fs->chmod($root . '/sites/default/services.yml', 0666);
      $event->getIO()->write('Create a sites/default/services.yml file with chmod 0666');
    }

    // Create the files directory with chmod 0777
    if (!$fs->exists($root . '/sites/default/files')) {
      $oldmask = umask(0);
      $fs->mkdir($root . '/sites/default/files', 0777);
      umask($oldmask);
      $event->getIO()->write('Create a sites/default/files directory with chmod 0777');
    }

    // Execute composer install defined on array $composer_json_mask_list
    $composerPathListToExec = array();
    foreach(self::$composer_json_mask_list as $composer_json_mask){
      $currentComposerList = glob($root.$composer_json_mask);
      if(is_array($currentComposerList)){
        $composerPathListToExec = array_merge($composerPathListToExec,$currentComposerList);
      }
    }
    if ($composerPathListToExec) {
      foreach ($composerPathListToExec as $composerPathToExec){
        exec('cd ' . dirname($composerPathToExec) . ' && composer install && cd -');
        $event->getIO()->write('Composer lauched on ' . $composerPathToExec);
      }
    }

    if (!$fs->exists($project_root . '/bin/spbuilder')) {
      $event->getIO()->write('Installing spbuilder in a separate folder');
      static::installSpbuilder();


      // Make symlink in bin/spbuilder
      $fs->symlink(
        '../' . static::$spbuilder_path . '/bin/spbuilder',
        './bin/spbuilder'
      );
      $event->getIO()->write('Create spbuilder symlink to ' . $project_root . '/bin/spbuilder');

//      // Symlink coding standards
//      if (!$fs->exists('./conf/phpcs-standards')
//        && $fs->exists('./' . static::$spbuilder_path . '/vendor/smile/php-codesniffer-rules/src')) {
//        $fs->mkdir($project_root . '/conf');
//        $fs->symlink(
//          '../../' . static::$spbuilder_path . '/vendor/smile/php-codesniffer-rules/src',
//          './conf/phpcs-standards'
//        );
//        $event->getIO()->write('Create conf/phpcs-standards symlink');
//      }
    }

    if ($fs->exists($project_root . '/bin/spbuilder')) {
      // Init spbuilder
      if (!$fs->exists($project_root . '/.spbuilder.yml')) {
        $event->getIO()->write('Init spbuilder for Drupal 8');
        static::initSpbuilder();
      }
    }
  }

  // Install SpBuilder in a separate path for dependencies independance purpose
  protected static function installSpbuilder()
  {
    // Install in $spbuilder_path
    exec('cd ' . static::$spbuilder_path . ' && composer install && cd -');
  }

  protected static function initSpbuilder()
  {
    exec('bin/spbuilder init drupal8');
  }

}
