from github import Github


g = Github('ece8441@upnet.gr', 'github password')

print(g.get_user())

for repo in g.get_user().get_repos():
    print(repo.name)
