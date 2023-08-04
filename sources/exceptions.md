# Principe des exceptions

```php
function toto()
{
    try {
        commande 1
        commande 2
        // Erreur
    } catch ($exception){
        Echo "erreur";
        // Ou
        throw new Exception('Une erreur est survenue');
    }
}

function tutu()
{
    try {
        toto();
    } catch ($exception) {
        echo $exception->getMessage();
    }
}
```

# Personnalisation des pages d'erreur symfony

```
templates/
└─ bundles/
   └─ TwigBundle/
      └─ Exception/
         ├─ error404.html.twig
         ├─ error403.html.twig
         └─ error.html.twig      # All other HTML errors (including 500)```