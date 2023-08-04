# MCD pour Mocodo 

```
USER: email, username, role
Belongs to, 0N GENRE, 0N MOVIE
GENRE: genre_code, name

Reviews, 0N MOVIE, 0N USER: content, rating
MOVIE: ISAN, title, release_date, duration, summary, synopsis, poster, rating
Has, 0N MOVIE, 11 SEASON

PERSON: person_code, firstname, lastname
Acting in, 0N PERSON, 0N MOVIE: role, credit_order
SEASON: season_code, season_number, episodes_number
```