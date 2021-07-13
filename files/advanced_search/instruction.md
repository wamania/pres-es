# Création de l'index

```shell
curl -X PUT "localhost:9200/search" -H 'Content-Type: application/json' --data-binary '@mapping.json'
```

# Indexation
```shell
curl -X POST -o - "localhost:9200/_bulk?pretty" -H 'Content-Type: application/json' --data-binary '@body_bulk.txt'
```
