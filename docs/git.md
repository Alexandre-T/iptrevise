# to save the changes in local directory

git commit -m "message"

# to save changes in your local github

git push

#to save changes in Alexandre-T/iptrevise

->log in github and do a pull request

#to cancel changes

git checkout -- composer.lock

# Add the remote, call it "upstream":

git remote add upstream https://github.com/whoever/whatever.git

# Fetch all the branches of that remote into remote-tracking branches,
# such as upstream/master:

git fetch upstream

# Make sure that you're on your master branch:

git checkout master

# Rewrite your master branch so that any commits of yours that
# aren't already in upstream/master are replayed on top of that
# other branch:

git rebase upstream/master
