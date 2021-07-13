## multi_match with weight example
```json
{
  "query": {
    "multi_match": {
      "fields": [
        "label^5",
        "original_label"
      ],
      "query": "star"
    }
  }
}
```

## dis_max

```json
{
  "from": 0,
  "size": 10,
  "query": {
    "bool": {
      "must": [
        {
          "dis_max": {
            "queries": [
              {
                "match": {
                  "label.keyword": {
                    "query": "star",
                    "boost": 40,
                    "operator": "and"
                  }
                }
              },
              {
                "match": {
                  "original_label.keyword": {
                    "query": "star",
                    "boost": 40,
                    "operator": "and"
                  }
                }
              },
              {
                "match": {
                  "label.lowercase": {
                    "query": "star",
                    "boost": 20,
                    "operator": "and"
                  }
                }
              },
              {
                "match": {
                  "original_label.lowercase": {
                    "query": "star",
                    "boost": 20,
                    "operator": "and"
                  }
                }
              },
              {
                "match": {
                  "text_search_data.keyword": {
                    "query": "star",
                    "boost": 5,
                    "operator": "and"
                  }
                }
              },
              {
                "match": {
                  "text_search_data.lowercase": {
                    "query": "star",
                    "boost": 2,
                    "operator": "and"
                  }
                }
              },
              {
                "match": {
                  "search_label.autocomplete": {
                    "query": "star",
                    "boost": 10,
                    "operator": "and"
                  }
                }
              },
              {
                "match": {
                  "label.autocomplete_full": {
                    "query": "star",
                    "boost": 15,
                    "operator": "and"
                  }
                }
              },
              {
                "match": {
                  "original_label.autocomplete_full": {
                    "query": "star",
                    "boost": 15,
                    "operator": "and"
                  }
                }
              },
              {
                "match": {
                  "text_search_data.autocomplete": {
                    "query": "star",
                    "boost": 1,
                    "operator": "and"
                  }
                }
              }
            ],
            "tie_breaker": 1
          }
        }
      ]
    }
  }
}
```

## Autocomplete
```json
{
    "query": {
        "match": {
            "label.autocomplete": "sta"
        }
    }
}
```
```json
{
    "text": "star wars",
    "analyzer": "fr_edge_ngram_2_10_analyzer"
}
```

## Suggest / spell checking
```json
{
  "suggest": {
    "text": "star wirs",
    "proposals": {
      "phrase": {
        "field": "search_label.trigram",
        "size": 1,
        "max_errors": 4,
        "direct_generator": [
          {
            "field": "search_label.trigram",
            "suggest_mode": "always"
          },
          {
            "field": "search_label.reverse",
            "suggest_mode": "always",
            "pre_filter": "reverse_analyzer",
            "post_filter": "reverse_analyzer"
          }
        ],
        "collate": {
          "query": {
            "source": {
              "match": {
                "{{field_name}}": {
                  "query": "{{suggestion}}",
                  "analyzer": "lowercase_analyzer",
                  "operator": "and"
                }
              }
            }
          },
          "params": {
            "field_name": "search_label.lowercase"
          },
          "prune": false
        }
      }
    }
  }
}
```

## Inner hits
```json
{
  "query": {
    "bool": {
      "must": [
        {
          "match": {
            "content": "futuriste"
          }
        },
        {
          "nested": {
            "path": "kinds",
            "query": {
              "match": {
                "kinds.name": "Action"
              }
            },
            "inner_hits": {}
          }
        }
      ]
    }
  }
}
```
