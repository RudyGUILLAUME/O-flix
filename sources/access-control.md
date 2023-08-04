```yaml

access_control:

        # Penser à utiliser bin/console debug:router pour avoir tous les motifs d'URL sous les yeux

    # BACK
        # page édition création (les castings seront pris en compte aussi)
        - { path: ^/back/(.*)/(new|edit), roles: ROLE_ADMIN}
        # Autre option possible
        # - { path: ^/back/(movie|user)/new, roles: ROLE_ADMIN}
        # - { path: ^/back/(movie|user)/\d+\edit, roles: ROLE_ADMIN}
        # suppression que pour les admins (méthode HTTP POST)
        - { path: ^/back/(.*)/\d+$, roles: ROLE_ADMIN, methods: [POST]} 
        # back office pour les managers, uniquement en consultation
        - { path: ^/back/, roles: ROLE_MANAGER }

    # FRONT
        # les utilisateurs doivent avoir accès aux avis
        - { path: ^/movie/\d+/review/add, roles: ROLE_USER }
        # les utilisateurs doivent avoir accès à leur liste
        - { path: ^/favorites, roles: ROLE_USER }

role_hierarchy:
        # MANAGER hérite du rôle USER
        # => si on souhaite que les managers et admins postent des critiques en leur nom
        ROLE_MANAGER: ROLE_USER
        # ADMIN hérite du rôle MANAGER
        ROLE_ADMIN: ROLE_MANAGER
```
