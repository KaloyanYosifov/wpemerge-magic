# WPEmerge Magic Cli tool

---

### Questions:

- *What is this for?*
WPEmerge magic cli tool is useful for automatically initialize WPEmerge in you WordPress projects.
Commands to create templates(controllers or views).

- *Why create this when there is wpemerge-cli*
I was thinking about that aswell, but i found that documentation for cli is in start theme. And as you can still include wp-cli to your project. I think that WPEmerge Magic stands out with it's init command where it automatically initialize a WPEmerge project without you doing much configuration

### How To initialize a project

- Run `composer require KaloyanYosifov/wpemerge-magic`.
- Run `./vendor/bin/magic init`.
- Go to your `functions.php` file and add on top of it:
`require_once __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'bootstrap.php';`