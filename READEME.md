# WP Deploy

Easily deploy a WordPress website, including the database, from development to production using a small PHP script and a couple of git hooks.

I've worked with WordPress for many years and I've always wanted a tool that would let me easily sync or deploy a website from development straight to production.

This system uses 2 git hooks and a small PHP script to do this.

The pre-commit hook on the development server triggers the PHP script to run mysqldump, using the values defined in `wp-config.php`, then `sed` is used to replace the development URL scheme and hostname with the production equivalents. After the URL replacement, the sql file is added to the commit.

The post-merge hook on the production server imports the committed mysql dump, then deletes the dump file.

Once everything is set up, deploying your WordPress site will be as simple as running `git pull origin master` , or whatever branch you choose, on your production server.

## Getting Started

Coming soon.

### Prerequisites

Your `wp-content/uploads/` directory should be in version control. If it's not, you'll need to synchronize your uploads manually, or use a different system.

What you'll need installed on your development and production servers:

```
PHP7+
git
sed
mysql
```

### Installing

Copy the file `/git_hooks/pre-commit` to `.git/hooks/` on your development server.
Copy the file `/git_hooks/post-merge` to `.git/hooks/` on your production server.
Copy the file `wp-deploy.php` to `/` on your development server.

Update the variables `$productionSchemeHostname` and `$devSchemeHostname` in `wp-deploy.php` with your values.

If WordPress is not installed in the root of your git directory, some paths in the git hooks will need to be updated.

## Versioning

I will use [SemVer](http://semver.org/) for versioning.

## Authors

* **Casey Smith** - *Initial work* - [Tallahasseean](https://github.com/Tallahasseean)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* TBD
