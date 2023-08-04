
## Requetes pour les pages

### Récupérer tous les films.

```sql
SELECT * FROM movie
```

### Récupérer les acteurs et leur(s) rôle(s) pour un film donné.

```sql
SELECT movie.title, role, credit_order, firstname, lastname FROM movie
INNER JOIN casting on casting.movie_id = movie.id
INNER JOIN person on person.id = casting.person_id
WHERE movie.title = 'Ted'
order by credit_order
```

### Récupérer les genres associés à un film donné.

```sql
SELECT title, name FROM movie
INNER JOIN movie_genre ON movie_genre.movie_id = movie.id
INNER JOIN genre ON genre.id = movie_genre.genre_id
WHERE movie.title = 'Ted'
```

### Récupérer les saisons associées à un film/série donné.

```sql
SELECT movie.title, number as saison, episodes_count FROM movie
JOIN season ON movie_id = movie.id
WHERE movie.title = 'How to Train Your Dragon (2010)'
```

### Récupérer les critiques pour un film donné.

```sql
SELECT movie.title, review.rating ,content FROM movie 
JOIN review ON movie_id = movie.id 
WHERE movie.title = "Pirates of the Caribbean: Dead Man's Chest"
```

### Récupérer les critiques pour un film donné, ainsi que le nom de l'utilisateur associé.

```sql
SELECT movie.title, content, nickname FROM movie
INNER JOIN review ON review.movie_id = movie.id
INNER JOIN user ON user.id = review.user_id
WHERE movie.title = 'Ted'
```

### Calculer, pour chaque film, la moyenne des critiques par film (en une seule requête).

```sql
/* avg = average = moyenne */
select avg(rating), title from review 
INNER JOIN movie on review.movie_id = movie.id
group by title
```

### Idem pour un film donné.

```sql
select avg(rating), title from review  
INNER JOIN movie on review.movie_id = movie.id 
where movie.title= 'Ted'
```

### Requêtes de recherche

### Récupérer tous les films pour une année de sortie donnée.

```sql
SELECT movie.title FROM movie
WHERE release_date LIKE '%1982%'
```

### Récupérer tous les films pour un titre donné (par ex. 'Epic Movie').

```sql
select * FROM movie where title = 'Epic Movie'
```

### Récupérer tous les films dont le titre contient une chaîne donnée.

```sql
SELECT title FROM movie
WHERE title LIKE '%The%'
```

'%' est l'équivalent de '*' pour le système de fichier Unix

## Pagination

Nombre de films par page : 10 (par ex.)

### Récupérer la liste des films de la page 2 (grâce à LIMIT).

```sql
SELECT * FROM movie 
LIMIT 10
OFFSET 10
```

### Testez la requête en faisant varier le nombre de films par page et le numéro de page.

```sql
SELECT * FROM movie 
LIMIT 10
OFFSET 2
```

## Contraintes sur le schéma SQL

Modifiez votre schéma pour permettre :

### Qu'il ne puisse y avoir deux films identiques.

- Identifier les autres champs uniques si existants.

Créer un index UNIQUE sur les clés à vérifier. Cela peut-être le titre par exemple, ou bien l'association titre + annee_de_sortie

### Qu'une critique puisse être associée à aucun utilisateur (anonyme).

Le champ user_id de l'entité review doit pouvoir etre nul

### Que la suppression d'un film implique la suppression de l'intégralité de son contenu (cascade).

je définis une contrainte de type 
```sql
CONSTRAINT `name`
    FOREIGN KEY (`...`)
    REFERENCES `table` (`champ`)
    /* MySQL fera les suppresions en cascade si besoin */
    ON DELETE CASCADE
```

### Que la suppression d'un utilisateur conserve ses critiques.
```sql
CONSTRAINT `name`
    FOREIGN KEY (`...`)
    REFERENCES `table` (`champ`)
    /* MySQL fera les suppresions en cascade si besoin */
    ON DELETE NO ACTION

```
# Foreign keys et UNIQUE Indexes

Coment définir un index de type 'Unique' sur une table. Ici un test d'unicité sur les champs 'title' et 'release_date' du film

```sql
DROP TABLE IF EXISTS `movie`;
CREATE TABLE `movie` 
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `release_date` date NOT NULL,
  `duration` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_release_date` (`title`,`release_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

Défiition de 'FOREIGN KEYS' sur la table movie_genre. C'est la table de jointure qui relie 'movie' à 'genre'

```sql
DROP TABLE IF EXISTS `movie_genre`;
CREATE TABLE `movie_genre` (
  /* champs qui permettent la jointure entre les deyux tables */
  `movie_id` int(10) unsigned NOT NULL,
  `genre_id` int(10) unsigned NOT NULL,
  /* Clé primaire sur les deux champs ci-dessus */
  PRIMARY KEY (`movie_id`,`genre_id`),
  /* MySQL doit avoir les index (key) des champs sur lesquels il y
     a des contraintes */
  KEY `genre_id` (`genre_id`),
  KEY `movie_id` (`movie_id`),
  /* Contrainte entre movie_id de cette table et le id de la table movie */
  CONSTRAINT `movie_genre_ibfk_1`
    FOREIGN KEY (`movie_id`)
    REFERENCES `movie` (`id`)
    /* MySQL fera les suppresions en cascade si besoin */
    ON DELETE CASCADE,
  /* Contrainte entre genre_id de cette table et le id de la table genre */
  CONSTRAINT `movie_genre_ibfk_2`
    FOREIGN KEY (`genre_id`)
    REFERENCES `genre` (`id`)
    /* MySQL fera les suppresions en cascade si besoin */
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


```