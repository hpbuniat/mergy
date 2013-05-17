mergy - a svn cherry-pick-assistant
=====

Idea
-----

The inspiration came from John Resig's pulley (http://ejohn.org/blog/pulley/)

mergy can assist in feature-branch driven development.
If you're pushing all, ticket-related or not, commits to a single branch, where
you're picking specific revision of important changes or finished tickets to merge
them into your stable trunk - mergy can do a lot of repeating tasks for you:

- create diff before merge, for quick review without additional tools like reviewboard
- selecting un-merged revision from branches
- filtering important and ticket-related revision
- auto-commit to trunk
- auto-update remote servers
- execute pre- and post-merge actions (e.g. phpunit)

```

Usage:
-----

mergy [--remote=[repository|branch]]     // remote repository, might be only a branch-name
      [--rev=revision[,revision]]        // revisions to merge (might have been merged before)
      [--ticket=ticket-id[,ticket-id]]   // find all revisions of a ticket
      [--continue]                       // continue skips the pre-merge-actions (e.g. after conflict)
      [--reintegrate]                    // reintegrate a whole branch - without specific revisions
      [--list]                           // list unmerged revisions from repository
      [--list-group]                     // list unmerged revisions from repository
      [--diff]                           // create a diff, based on the revisions to merge
      [--all]                            // use all unmerged revisions
      [--diff-all]                       // equals --diff --all
      [--strict]                         // only merge, what was given - no force via config, this is the default
      [--commit]                         // commit changes in the wc - with tracked log, if present (only, if unattended)
      [--more]                           // skip commit
      [--unattended]                     // skip optional confirmations

      // further parameters
      [--verbose]                        // verbose
      [--force=keyword[,keyword]]        // keywords to force merge of this revisons, if unmerged
      [--config=mergy.json]              // use this config-file
      [--formatter=text]                 // use a specific formatter - only for --list
      [--path=[PATH_TO_WC]]              // use this working copy (instead of .)

```

### Use case:

// get a quick overview about which revisions of your remote are currently unmerged
`mergy --list --all`

// get a quick overview about which revisions of your remote are currently unmerged and group them by ticket
`mergy --list-group --all`

// merge all revisions of ticket #1000 and include revision 523, as it does not contain a proper commit-message
// also ignore all force-comments
`mergy --ticket=1000 --rev=523 --strict --more`

// mergy will can now create a diff of everything, was is going to be merged.
// After reviewing the diff and merging, a phpunit run should verify the merge-result.

// additionally merge all revisions of ticket #1002 and all revisions with force comments (e.g. !merge)
`mergy --ticket=1002 --continue`

// view a list of remaining unmerged revisions
`mergy --list --continue`
