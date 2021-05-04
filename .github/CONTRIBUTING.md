- [Assignment](#assignment)
- [Development Environment](#development-environment)
- [Feature Branch Workflow](#feature-branch-workflow)
- [Cleanup Before Pull Request](#cleanup-before-pull-request)
- [Pull Request](#pull-request)

## Assignment

Unless otherwise specified, any issue that does not have an assignee can be claimed by any volunteer.

Leave a comment in the issue indicating interest so that the assignee can be designated by a member. This is a requirement in Github to allow assignment of contributors outside of the organization by members. 

If further discussion is needed before work can be started, please make use of the `#development` channel on the discord.

## Development Environment

Follow the [Installation Guide](/AnimeThemes/animethemes-server/wiki/Installation) to ensure a functional local instance of AnimeThemes forked from this repository.

Enable the forked repository in [StyleCI](https://styleci.io/) so that code styling guidelines are followed. At the time of writing, analysis is run on push and merge.

Set this repository as upstream to the forked repository.

`git remote add upstream https://github.com/AnimeThemes/animethemes-server.git`

Check out release branch. At the time of writing, the release branch is named `wiki`.

`git checkout wiki`

Pull upstream changes so that the local wiki branch is even with the upstream wiki branch.

`git pull upstream wiki`

Push upstream changes to the forked remote wiki branch if needed.

`git push`

## Feature Branch Workflow

Once the release branch is even with upstream, create a new feature branch from the release branch. The new feature branch name should be descriptive.

`git branch new-feature-branch`

Switch to the new feature branch.

`git checkout new-feature-branch`

Make changes in the new feature branch. Once completed, run tests.

`php artisan config:clear && php artisan test --parallel`

Execute static code analysis.

`./vendor/bin/phpstan analyse --memory-limit=-1`

Stage changes. Commit changes. Please include ticket ID's and use [Semantic Commit Messages](https://gist.github.com/joshbuchea/6f47e86d2510bce28f8e7f42ae84c716).

## Cleanup Before Pull Request

Fetch the upstream release branch and merge with the fork repo's release branch.
```
git fetch upstream wiki
git checkout wiki
git merge upstream/wiki
```

If there were any new commits, rebase the new feature branch.
```
git checkout new-feature-branch
git rebase wiki
```

## Pull Request

Push changes to remote.

`git push --set-upstream origin new-feature-branch`

Confirm StyleCI passing status on push.

Create Pull Request from the new feature branch to the upstream release branch.

## Pruning

Once the Pull Request is merged, delete the feature branch locally.

`git branch -d new-feature-branch`

Delete the remote feature branch.

`git push origin --delete new-feature-branch`

Pull upstream changes so that the fork release branch is even with upstream.

`git pull upstream release-branch`

Push upstream changes to the fork's remote if needed.

`git push`

## References

* [Semantic Commit Messages](https://gist.github.com/joshbuchea/6f47e86d2510bce28f8e7f42ae84c716)
* [Github Forking](https://gist.github.com/Chaser324/ce0505fbed06b947d962)
