## Assignment

Unless otherwise specified, any issue that does not have an assignee can be claimed by any volunteer.

Please leave a comment in the issue indicating interest so that you can be designated as the assignee by a member. This is a requirement in Github to allow asssignment of contributors outside of the organization by members. 

If further discussion is needed before work can be started, please make use of the `#design` channel on the discord.

## Development Environment

Ensure that you have a functional local instance of AnimeThemes forked from this repository by following the [Installation Guide](https://github.com/AnimeThemes/animethemes-server/wiki/Installation).

Enable your repository in [StyleCI](https://styleci.io/) so that code styling guidelines are followed. At the time of writing, analysis is run on push and merge.

Set this repository as upstream to your fork.

`git remote add upstream https://github.com/AnimeThemes/animethemes-server.git`

Checkout release branch.

`git checkout release-branch`

Pull upstream changes so that your local branch is even with upstream.

`git pull upstream release-branch`

Push upstream changes to your remote if needed.

`git push`

## Feature Branch Workflow

Once your release branch is even with upstream, create feature branch from release branch.

`git branch new-feature-branch`

Switch to feature branch.

`git checkout new-feature-branch`

Make changes in feature branch. Once completed, run tests.

`php artisan config:clear && php artisan test`

Execute static code analysis.

`./vendor/bin/phpstan analyse --memory-limit=-1`

Stage changes. Commit changes. Please include ticket ID's and use [Semantic Commit Messages](https://gist.github.com/joshbuchea/6f47e86d2510bce28f8e7f42ae84c716).

## Cleanup Before Pull Request

Fetch upstream master and merge with your repo's master branch
```
git fetch upstream release-branch
git checkout release-branch
git merge upstream/release-branch
```

If there were any new commits, rebase your development branch
```
git checkout new-feature-branch
git rebase release-branch
```

## Pull Request

Push changes to remote.

`git push --set-upstream origin new-feature-branch`

Confirm StyleCI passing status on push.

Create Pull Request from your new feature branch to upstream release branch.

## Pruning

Once the Pull Request is merged, delete the feature branch locally.

`git branch -d new-feature-branch`

Delete the remote feature branch.

`git push origin --delete new-feature-branch`

Pull upstream changes so that your local branch is even with upstream.

`git pull upstream release-branch`

Push upstream changes to your remote if needed.

`git push`

## References

* [Semantic Commit Messages](https://gist.github.com/joshbuchea/6f47e86d2510bce28f8e7f42ae84c716)
* [Github Forking](https://gist.github.com/Chaser324/ce0505fbed06b947d962)
