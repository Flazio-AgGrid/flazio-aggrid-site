# flazio-aggrid-site
## Dévelopement
- Pour chaque nouvelle fonctionnalité, une **nouvelle *branch*** portant le nom de celle-ci devra être créer.
- Une fois que votre fonctionnalité est fini de dev vous allez pouvoir **fusionner sur la branch dev**. (Pensez à *commit* avant de fusionner pour garder une trace de vos modifications)
- A noté que toute créations de commit, branch, 
## Commande à savoir
### Récupérer le *repository* ou le mettre à jour 
Pour *clone* le *repository* :
```
git clone <lien de repo>
```
Pour récupérer la nouvelle version du *repository* :
```
git pull
```
### Faire un *commit*
Pour effectué un *commit* (le fait d'envoyé son code qu'on vient de modifié sur le *repository*):
- Indexé les fichiers modifié dans le *commit*
```
git add <nom du/des fichier(s) ou . pour tout mettre mais il y a un risque de conflit>
```
- Créer le *commit* avec une description sur le contenue de votre *commit*
```
git commit -m <description de votre commit>
```
- Pour *push* (publié) votre commit sur le *repository* :
```
git push
```
### Les branches
- Pour connaitre la *branch* où vous vous trouvrez :
```
git branch
```
- Pour changez de *branch* :
```
git checkout <branch>
```
- Pour créez une nouvelle *branch* et se mettre dessus directement:
```
git checkout -b <branch>
```
### La fusion
Pour fusionner (merge) votre **fonctionnalité** sur le *repository* **dev** :
- Avant de fusionner, vous devez vous trouver dans la branch que vous voulez fusionner.
```
git checkout dev
```
- Un fois dans la bonne *branch*, vous pouvez fusionner (pensez à *commit* tous vos changements avant de fusionné):
```
git merge <nom de votre branch à fusionner>
```
- Une fois la fusion effectuée, pensez à supprimer la *branch* de votre fonctionnalité :
```
git branch -d <branch>
```
