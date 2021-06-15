# Installation

## download elasticsearch
```shell
wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-7.13.1-linux-x86_64.tar.gz
```
Extraire puis lancer avec
```shell
bin/elasticsearch
```
Vérifier le fonctionnement sur http://localhost:9200

Alternative docker single ou multi node : 
https://www.elastic.co/guide/en/elasticsearch/reference/current/docker.html

## Download cerebro
```shell
wget https://github.com/lmenezes/cerebro/releases/download/v0.9.4/cerebro-0.9.4.tgz
```
Extraire puis lancer avec 
```shell
bin/cerebro
```

Verifier le fonctionnement de cerebro et la connexion à ES sur
http://localhost:9000/#/rest?host=http:%2F%2Flocalhost:9200

## Création de l'index

La définition de l'index est dans mapping.json

```shell
curl -X PUT "localhost:9200/review_1506" -H 'Content-Type: application/json' --data-binary '@mapping.json'
```

## Indexation

### Mono
POST /review_1506/_doc/{id}

### Bulk
curl -X POST -o - "localhost:9200/_bulk?pretty" -H 'Content-Type: application/json' --data-binary '@body_bulk.txt'

### Links
 - https://www.elastic.co/guide/en/elasticsearch/reference/current/query-filter-context.html
 - https://www.elastic.co/guide/en/elasticsearch/reference/master/tune-for-indexing-speed.html
