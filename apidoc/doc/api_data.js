define({ "api": [
  {
    "type": "get",
    "url": "index.php?route=module/apimodule/orderhistory",
    "title": "getOrderHistory",
    "name": "getOrderHistory",
    "group": "All",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "order_id",
            "description": "<p>unique order ID.</p>"
          },
          {
            "group": "Parameter",
            "type": "Token",
            "optional": false,
            "field": "token",
            "description": "<p>your unique token.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "version",
            "description": "<p>Current API version.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Status of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "order_status_id",
            "description": "<p>ID of the status of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "Date",
            "optional": false,
            "field": "date_added",
            "description": "<p>Date of adding status of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "comment",
            "description": "<p>Some comment added from manager.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "statuses",
            "description": "<p>Statuses list for order.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n  {\n      \"response\":\n          {\n              \"orders\":\n                 {\n                     {\n                         \"name\": \"Отменено\",\n                         \"order_status_id\": \"7\",\n                         \"date_added\": \"2016-12-13 08:27:48.\",\n                         \"comment\": \"Some text\"\n                     },\n                     {\n                         \"name\": \"Сделка завершена\",\n                         \"order_status_id\": \"5\",\n                         \"date_added\": \"2016-12-25 09:30:10.\",\n                         \"comment\": \"Some text\"\n                     },\n                     {\n                         \"name\": \"Ожидание\",\n                         \"order_status_id\": \"1\",\n                         \"date_added\": \"2016-12-01 11:25:18.\",\n                         \"comment\": \"Some text\"\n                      }\n                  },\n               \"statuses\":\n                   {\n                        {\n                             \"name\": \"Отменено\",\n                             \"order_status_id\": \"7\",\n                             \"language_id\": \"1\"\n                        },\n                        {\n                             \"name\": \"Сделка завершена\",\n                             \"order_status_id\": \"5\",\n                             \"language_id\": \"1\"\n                         },\n                         {\n                             \"name\": \"Ожидание\",\n                             \"order_status_id\": \"1\",\n                             \"language_id\": \"1\"\n                         }\n                    }\n          },\n      \"status\": true,\n      \"version\": 1.0\n  }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n{\n     \"error\": \"Can not found any statuses for order with id = 5\",\n     \"version\": 1.0,\n     \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "../modules/apimodule/controllers/front/orders.php",
    "groupTitle": "All"
  }
] });
