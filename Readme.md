#TYPO3 Namespacer

A script to create TYPO3 extension migration files when moving to namespaces.

##Usage

Simply drop migrate.php into your extension folder and adjust the target vendor
name at the beginning of the script. Then run `php migrate.php`.

This will result in two files being created:

* `Migrations/Code/ClassAliasMap.php` - allows backwards compatibility for extensions still using the old non-namespaced class names
* `Migrations/Code/LegacyClassesForIde.php` - allows IDEs to provide code completion on old non-namespaced class usages and mark them as deprecated

##Requirements

Needs PHP 5.4.32+ or 5.5.16+

##Why

We released the Apache Solr for TYPO3 extension (EXT:solr) in version 3.0 with
the goal to be compatible with TYPO3 LTS versions 4.5 and 6.2. To achieve that
we couldn't use PHP namespaces yet. The next version is supposed to drop support
for TYPO3 version below 6.2 and introduce namespaces.

The namespace migration was started and by now has been finished, all classes in
EXT:solr now have namespaces. However, during the migration it turned out that
the decision might have been made a bit too fast. A couple bugs turned up caused
by TYPO3 referencing old class names. Also, simply moving to namespaces would
have caused breaking backwards compatibility with other extensions using EXT:solr.

Unfortunately a lot of classes had been moved to namespaces already when we
realized the impact - oops. Creating these migration files manually would have
been a tedious and time consuming task. To save us a lot of work this script was
created.

The script creates the migration files and should be a good starting point even
if you moved classes and interfaces around like we did. Some manual tweaking
might still be necessary, but still saving a lot of work.
