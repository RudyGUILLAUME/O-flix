Challenge du jour

Front
- Ajouter une critique en POST
    - https://symfony.com/doc/current/testing.html#submitting-forms
    - Avec erreurs du form (Validation)
    - Sans erreurs (redirection vers le film)

- Ma liste
    - Ajout
    - Suppression
    - Vider


Back
Commme dans /tests/Controller/Back/AnonymousAccessTest.php

- Anonyme, routes en POST
- idem pour
    - ROLE_USER
    - ROLE_MANAGER => GET ok, POST pas ok
    - ROLE_ADMIN => GET + POST et analyse du retour selon l'action (DELETE => 303)

API (Bonus)
    - Test JWT ?? voir la doc de Lexik