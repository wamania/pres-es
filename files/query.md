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

# Aggregation
## simple avg
```json
{
    "size": 0,
    "aggs": {
        "avg_rating": {
            "avg": {
                "field": "note.float"
            }
        }
    }
}
```

## simple buckets
```json
{
  "size": 0,
  "aggs": {
    "platforms_name": {
      "terms": {
        "field": "platforms.name"
      }
    }
  }
}
```

## Sub-aggregation avg + order
```json
{
  "size": 0,
  "aggs": {
    "platforms": {
      "terms": {
        "field": "platforms.name",
        "order": { "avg_rating": "desc" }
      },
      "aggs": {
        "avg_rating": {
          "avg": {
            "field": "note.float"
          }
        }
      }
    }
  }
}
```

Sub aggregation buckets
```json
{
  "size": 0,
  "aggs": {
    "platforms_id": {
      "terms": {
        "field": "kinds.name"
      },
      "aggs": {
        "platforms_name": {
          "terms": {
            "field": "platforms.name"
          }
        }
      }
    }
  }
}
```

### Nested
Bad query
```json
{
  "size": 0,
  "aggs": {
    "platforms_id": {
      "terms": {
        "field": "kinds.id"
      },
      "aggs": {
        "platforms_name": {
          "terms": {
            "field": "kinds.name"
          }
        }
      }
    }
  }
}
```

Good query
```json
{
  "size": 0,
  "aggs": {
    "platforms_id": {
      "nested": {
        "path": "kinds"
      },
      "aggs": {
        "platforms_id": {
          "terms": {
            "field": "kinds.id"
          },
          "aggs": {
            "platforms_name": {
              "terms": {
                "field": "kinds.name"
              }
            }
          }
        }
      }
    }
  }
}
```

Reverse nested : 
```json
{
  "size": 0,
  "aggs": {
    "platforms_id": {
      "nested": {
        "path": "kinds"
      },
      "aggs": {
        "platforms_id": {
          "terms": {
            "field": "kinds.id"
          },
          "aggs": {
            "platforms_name": {
              "terms": {
                "field": "kinds.name"
              }
            },
            "kind_to_root": {
              "reverse_nested": {}, 
              "aggs": {
                "platforms_by_kinds": {
                  "terms": {
                    "field": "platforms.name"
                  }
                }
              }
            }
          }
        }
      }
    }
  }
}
```

Top hits
```json
{
  "query": {
    "bool": {
      "must": [
        {
          "match": {
            "content": "futuriste"
          }
        }
      ]
    }
  },
  "size": 0,
  "aggs": {
    "platforms_name": {
      "terms": {
        "field": "platforms.name"
      },
      "aggs": {
        "top_hits_by_platforms": {
          "top_hits": {
            "sort": [
              {
                "_score": {
                  "order": "desc"
                }
              }
            ],
            "size": 3
          }
        }
      }
    }
  }
}
```

## Pipeline aggregation
```json
{
  "size": 0,
  "aggs": {
    "published_histo": {
      "date_histogram": {
        "field": "published_at",
        "calendar_interval": "year"
      },
      "aggs": {
        "max_rating": {
          "max": {
            "field": "note.float"
          }
        },
        "moyenne_mobile": {
          "moving_avg": { 
              "buckets_path": "max_rating",
              "window": 9
              
          } 
        }
      }
    }
  }
}
```
