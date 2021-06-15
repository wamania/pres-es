# Queries

## simple match
```json
{
  "query": {
    "match": {
      "content": "battle"
    }
  }
}
```

## nested

5 résultats
```json
{
  "query": {
    "bool": {
      "filter": [
        {
          "term": {
            "kinds.name": "Wargame"
          }
        },
        {
          "term": {
            "kinds.player": "meuporg"
          }
        }
      ]
    }
  }
}
```

3 résultats
```json
{
  "query": {
    "bool": {
      "filter": [
        {
          "nested": {
            "path": "kinds",
            "query": {
              "bool": {
                "filter": [
                  {
                    "term": {
                      "kinds.name": "Wargame"
                    }
                  },
                  {
                    "term": {
                      "kinds.player": "meuporg"
                    }
                  }
                ]
              }
            }
          }
        }
      ]
    }
  }
}
```

### filter/must/must_not/should
```json
{
  "query": {
    "bool": {
      "filter": [
        {
          "nested": {
            "path": "platforms",
            "query": {
              "terms": {
                "platforms.name": [
                  "PC"
                ]
              }
            }
          }
        },
        {
          "nested": {
            "path": "kinds",
            "query": {
              "bool": {
                "filter": [
                  {
                    "bool": {
                      "should": [
                        {
                          "term": {
                            "kinds.name": "Stratégie"
                          }
                        },
                        {
                          "term": {
                            "kinds.name": "Fps"
                          }
                        }
                      ]
                    }
                  },
                  {
                    "term": {
                      "kinds.player": "solo"
                    }
                  }
                ]
              }
            }
          }
        }
      ],
      "must": [
        {
          "multi_match": {
            "fields": [
              "title",
              "content"
            ],
            "query": "médiévale"
          }
        }
      ],
      "must_not": [
        {
          "match": {
            "content": "spatial"
          }
        },
        {
          "term": {
            "visibility": "none"
          }
        }
      ],
      "should": [
        {
          "match": {
            "title": "chevaliers"
          }
        }
      ]
    }
  }
}
```
