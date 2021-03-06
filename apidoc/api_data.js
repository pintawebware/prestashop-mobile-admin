define({ "api": [
  {
    "type": "post",
    "url": "index.php?action=deleteimage&fc=module&module=apimodule&controller=products",
    "title": "deleteImage",
    "name": "deleteImage",
    "group": "All",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Token",
            "optional": false,
            "field": "token",
            "description": "<p>your unique token.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "product_id",
            "description": "<p>product ID.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "image_id",
            "description": "<p>image ID.</p>"
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
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n{\n  \"Status\" : true,\n  \"version\": 1.0\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"Error\" : \"Can not found image with id = 10\",\n     \"version\": 1.0,\n     \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/products.php",
    "groupTitle": "All"
  },
  {
    "type": "get",
    "url": "index.php?action=getcategories&fc=module&module=apimodule&controller=products",
    "title": "getCategoriesList",
    "name": "getCategoriesList",
    "group": "All",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Token",
            "optional": false,
            "field": "token",
            "description": "<p>your unique token.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "category_id",
            "description": "<p>unique category ID.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "version",
            "description": "<p>Current API version.</p>"
          },
          {
            "group": "Success 200",
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.categories",
            "description": "<p>Array of categories.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "response.categories.name",
            "description": "<p>Category name.</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "response.categories.parent",
            "description": "<p>Is there a parent category ?</p>"
          },
          {
            "group": "Success 200",
            "type": "string",
            "optional": false,
            "field": "response.categories.category_id",
            "description": "<p>Category id.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": " HTTP/1.1 200 OK {\n      \"status\": true,\n      \"response\": {\n      \"images\": [\n      {\n      \"image_id\": -1,\n      \"image\": \"\"\n      },\n      {\n      \"image\": \"http://prestashop1721.pixy.pro/36-home_default/erttre.jpg\",\n      \"image_id\": \"36\"\n      },\n      {\n      \"image\": \"http://prestashop1721.pixy.pro/37-home_default/erttre.jpg\",\n      \"image_id\": \"37\"\n      },\n      {\n      \"image\": \"http://prestashop1721.pixy.pro/38-home_default/erttre.jpg\",\n      \"image_id\": \"38\"\n      },\n      {\n      \"image\": \"http://prestashop1721.pixy.pro/39-home_default/erttre.jpg\",\n      \"image_id\": \"39\"\n      },\n      {\n      \"image\": \"http://prestashop1721.pixy.pro/40-home_default/erttre.jpg\",\n      \"image_id\": \"40\"\n      },\n      {\n      \"image\": \"http://prestashop1721.pixy.pro/41-home_default/erttre.jpg\",\n      \"image_id\": \"41\"\n      },\n      {\n      \"image\": \"http://prestashop1721.pixy.pro/42-home_default/erttre.jpg\",\n      \"image_id\": \"42\"\n      }\n      ],\n\"product_id\": \"9\",\n\"vendor_code\": \"demo_3\",\n\"status_name\": \"Enabled\",\n\"categories\": [],\n\"options\": [],\n\"description\": \"\\u2028\",\n\"quantity\": \"3333\",\n\"price\": \"255.00\",\n\"name\": \"Printed%20Dress\",\n\"currency_code\": \"UAH\"\n},\n\"version\": 2\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"Error\" : \"Not one category not found\",\n     \"version\": 1.0,\n     \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/products.php",
    "groupTitle": "All"
  },
  {
    "type": "get",
    "url": "index.php?action=getproductbyid&fc=module&module=apimodule&controller=products",
    "title": "getProductInfo",
    "name": "getProductInfo",
    "group": "All",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Token",
            "optional": false,
            "field": "token",
            "description": "<p>your unique token.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "product_id",
            "description": "<p>unique product ID.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "version",
            "description": "<p>Current API version.</p>"
          },
          {
            "group": "Success 200",
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.product_id",
            "description": "<p>ID of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.quantity",
            "description": "<p>Actual quantity of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.name",
            "description": "<p>Name of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.description",
            "description": "<p>Detail description of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.status_name",
            "description": "<p>Status name ( Enabled / Disabled )</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.vendor_code",
            "description": "<p>Vendor code of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.currency_code",
            "description": "<p>Default currency of the shop.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.price",
            "description": "<p>Price of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.categories",
            "description": "<p>Array of the categories of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.categories.name",
            "description": "<p>Category name.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.categories.category_id",
            "description": "<p>Category id.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.options",
            "description": "<p>Array of the options of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.options.option_id",
            "description": "<p>Option id.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.options.option_name",
            "description": "<p>Option name.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.options.option_value_id",
            "description": "<p>Option value id.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.options.option_value_name",
            "description": "<p>Option value name.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.options.language_id",
            "description": "<p>Language id of options and option values.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.features",
            "description": "<p>Array of the features of the product</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.features.feature_id",
            "description": "<p>Feature id.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.features.language_id",
            "description": "<p>Language id of options and option values.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.features.feature_name",
            "description": "<p>Feature name.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.features.feature_value_id",
            "description": "<p>Feature value id.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.features.feature_value_name",
            "description": "<p>Feature value name</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.images",
            "description": "<p>Array of the images of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.images.image",
            "description": "<p>Image Link.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "response.images.image_id",
            "description": "<p>Image id. If the image is set as the main thing then its id (-1)</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": " HTTP/1.1 200 OK  {\n  \"status\": true,\n  \"response\": {\n      \"images\": [\n          {\n              \"image\": \"http://prestashop1721.pixy.pro/43-home_default/printed-chiffon-dress.jpg\",\n              \"image_id\": -1\n          },\n          {\n              \"image\": \"http://prestashop1721.pixy.pro/23-home_default/printed-chiffon-dress.jpg\",\n              \"image_id\": \"23\"\n          }\n      ],\n      \"product_id\": \"7\",\n      \"vendor_code\": \"\",\n      \"status_name\": \"Enabled\",\n      \"categories\": [\n          {\n              \"category_id\": \"3\",\n              \"name\": \"Women\"\n          },\n          {\n              \"category_id\": \"8\",\n              \"name\": \"Dresses\"\n          },\n          {\n              \"category_id\": \"11\",\n              \"name\": \"Summer Dresses\"\n          }\n      ],\n      \"options\": [\n          [\n              {\n                  \"option_value_id\": \"1\",\n                  \"option_id\": \"1\",\n                  \"language_id\": \"1\",\n                  \"option_value_name\": \"S\",\n                  \"option_name\": \"Размер\"\n              },\n              {\n                  \"option_value_id\": \"16\",\n                  \"option_id\": \"3\",\n                  \"language_id\": \"1\",\n                  \"option_value_name\": \"Жёлтый\",\n                  \"option_name\": \"Цвет\"\n              }\n          ],\n          [\n              {\n                  \"option_value_id\": \"2\",\n                  \"option_id\": \"1\",\n                  \"language_id\": \"1\",\n                  \"option_value_name\": \"M\",\n                  \"option_name\": \"Размер\"\n              },\n              {\n                  \"option_value_id\": \"16\",\n                  \"option_id\": \"3\",\n                  \"language_id\": \"1\",\n                  \"option_value_name\": \"Жёлтый\",\n                  \"option_name\": \"Цвет\"\n              }\n          ]\n      ],\n      \"features\": [\n         {\n             \"feature_id\": \"6\",\n             \"language_id\": \"1\",\n             \"feature_name\": \"Weight\",\n             \"feature_value_id\": \"18\",\n             \"feature_value_name\": \"1\"\n         },\n         {\n             \"feature_id\": \"7\",\n             \"language_id\": \"1\",\n             \"feature_name\": \"Volume\",\n             \"feature_value_id\": \"23\",\n             \"feature_value_name\": \"2\"\n          }\n       ],\n             \"description\": \"qwerty\",\n             \"quantity\": \"12\",\n             \"price\": \"123.00\",\n             \"name\": \"quantity\",\n             \"currency_code\": \"UAH\"\n         },\n  \"version\": 2\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"Error\" : \"Can not found product with id = 10\",\n     \"version\": 1.0,\n     \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/products.php",
    "groupTitle": "All"
  },
  {
    "type": "get",
    "url": "index.php?action=products&fc=module&module=apimodule&controller=products",
    "title": "getProductsList",
    "name": "getProductsList",
    "group": "All",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Token",
            "optional": false,
            "field": "token",
            "description": "<p>your unique token.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "page",
            "description": "<p>number of the page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "limit",
            "description": "<p>limit of the orders for the page.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>name of the product for search.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "version",
            "description": "<p>Current API version.</p>"
          },
          {
            "group": "Success 200",
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.products",
            "description": "<p>Array of products.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.product_id",
            "description": "<p>ID of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.vendor_code",
            "description": "<p>Vendor code of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.name",
            "description": "<p>Name of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.currency_code",
            "description": "<p>Default currency of the shop.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.price",
            "description": "<p>Price of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.quantity",
            "description": "<p>Actual quantity of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.image",
            "description": "<p>Url to the product image.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.category",
            "description": "<p>The category to which the product belongs.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n  \"status\": true,\n  \"errors\": [],\n  \"response\": {\n      \"products\": [\n          {\n              \"product_id\": \"9\",\n              \"vendor_code\": \"demo_3\",\n              \"quantity\": \"3333\",\n              \"image\": \"\",\n              \"price\": \"255.00\",\n              \"name\": \"Printed%20Dress\",\n              \"category\": \"Главная\",\n              \"currency_code\": \"UAH\"\n          },\n          {\n              \"product_id\": \"7\",\n              \"vendor_code\": \"\",\n              \"quantity\": \"12\",\n              \"image\": \"http://prestashop1721.pixy.pro/img/p/2/0/20.jpg\",\n              \"price\": \"123.00\",\n              \"name\": \"quantity\",\n              \"category\": \"Summer Dresses\",\n              \"currency_code\": \"UAH\"\n          }\n      ]\n  },\n  \"version\": 2\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"Error\" : \"Not one product not found\",\n     \"version\": 1.0,\n     \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/products.php",
    "groupTitle": "All"
  },
  {
    "type": "post",
    "url": "index.php?action=mainimage&fc=module&module=apimodule&controller=products",
    "title": "mainImage",
    "name": "mainImage",
    "group": "All",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Token",
            "optional": false,
            "field": "token",
            "description": "<p>your unique token.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "image_id",
            "description": "<p>main image ID.</p>"
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
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n{\n  \"Status\" : true,\n  \"version\": 1.0\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"Error\" : \"Could not found image with id = 10\",\n     \"version\": 1.0,\n     \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/products.php",
    "groupTitle": "All"
  },
  {
    "type": "post",
    "url": "index.php?action=updateproduct&fc=module&module=apimodule&controller=products",
    "title": "updateProduct",
    "name": "updateProduct",
    "group": "All",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Token",
            "optional": false,
            "field": "token",
            "description": "<p>Your unique token.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "product_id",
            "description": "<p>ID of the product.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "vendor_code",
            "description": "<p>Vendor code of the product.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>Name of the product.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "quantity",
            "description": "<p>Actual quantity of the product.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "price",
            "description": "<p>Price of the product.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "description",
            "description": "<p>Detail description of the product.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "description_short",
            "description": "<p>Short description of the product.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "categories",
            "description": "<p>Array of categories of the product.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "status",
            "description": "<p>Status of the product.</p>"
          },
          {
            "group": "Parameter",
            "type": "Array[File]",
            "optional": false,
            "field": "images",
            "description": "<p>Array of the images of the product.</p>"
          },
          {
            "group": "Parameter",
            "type": "Array",
            "optional": false,
            "field": "options",
            "description": "<p>Array of the product options.</p>"
          },
          {
            "group": "Parameter",
            "type": "Array",
            "optional": false,
            "field": "options.option_value_ids",
            "description": "<p>Array of combination of option_value_ids.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "version",
            "description": "<p>Current API version.</p>"
          },
          {
            "group": "Success 200",
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.product_id",
            "description": "<p>Unique product ID.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.images",
            "description": "<p>Array images of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.images.image",
            "description": "<p>Link image.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "response.images.image_id",
            "description": "<p>Image id.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "    HTTP/1.1 200 OK\n{\n  \"Response\":\n  {\n      \"product_id\" : \"1\",\n      \"images\": [\n              {\n                  \"image\": \"http://site-url/image/catalog/demo/htc_iPhone_1.jpg\",\n                  \"image_id\": -1\n              },\n              {\n                  \"image\": \"http://site-url/image/catalog/demo/htc_iPhone_1.jp\",\n                  \"image_id\": \"5\"\n              },\n              {\n                  \"image\": \"http://site-url/image/catalog/demo/htc_iPhone_1.jp\",\n                  \"image_id\": \"6\"\n              }\n         ]\n  },\n  \"Status\" : true,\n  \"version\": 1.0\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"Error\" : \"Can not found product with id = 10\",\n     \"version\": 1.0,\n     \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/products.php",
    "groupTitle": "All"
  },
  {
    "type": "post",
    "url": "/index.php?action=login&fc=module&module=apimodule&controller=auth",
    "title": "Login",
    "name": "Login",
    "group": "Auth",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "email",
            "description": "<p>User unique email.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "password",
            "description": "<p>User's  password.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "device_token",
            "description": "<p>User's device's token for firebase notifications.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "os_type",
            "description": "<p>android|ios</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "version",
            "description": "<p>Current API version.</p>"
          },
          {
            "group": "Success 200",
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "error",
            "description": "<p>Description error.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.token",
            "description": "<p>Token.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "  HTTP/1.1 200 OK\n{\n   \"error\": \"\",\n    \"version\": 1,\n    \"response\": {\n       \"token\": \"eb3d0b776b33638a5a34b0ed63b882b5\"\n    },\n    \"status\": true\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n{\n   \"error\": \"The password field is blank.\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/auth.php",
    "groupTitle": "Auth"
  },
  {
    "type": "post",
    "url": "/index.php?action=delete&fc=module&module=apimodule&controller=auth",
    "title": "deleteUserDeviceToken",
    "name": "deleteUserDeviceToken",
    "group": "Auth",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "old_token",
            "description": "<p>User's device's token for firebase notifications.</p>"
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
            "type": "Boolean",
            "optional": false,
            "field": "status",
            "description": "<p>true.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "  HTTP/1.1 200 OK\n{\n    \"response\":\n    {\n       \"status\": true,\n       \"version\": 1.0\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n{\n  \"error\": \"Missing some params\",\n  \"version\": 1.0,\n  \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/auth.php",
    "groupTitle": "Auth"
  },
  {
    "type": "post",
    "url": "/index.php?action=update&fc=module&module=apimodule&controller=auth",
    "title": "updateUserDeviceToken",
    "name": "updateUserDeviceToken",
    "group": "Auth",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "new_token",
            "description": "<p>User's device's new token for firebase notifications.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "old_token",
            "description": "<p>User's device's old token for firebase notifications.</p>"
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
            "type": "Boolean",
            "optional": false,
            "field": "status",
            "description": "<p>true.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "  HTTP/1.1 200 OK\n{\n    \"response\":\n    {\n       \"status\": true,\n       \"version\": 1.0\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n{\n  \"error\": \"Missing some params\",\n  \"version\": 1.0,\n  \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/auth.php",
    "groupTitle": "Auth"
  },
  {
    "type": "get",
    "url": "index.php?action=list&fc=module&module=apimodule&controller=clients",
    "title": "getClients",
    "name": "GetClients",
    "group": "Clients",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Token",
            "optional": false,
            "field": "token",
            "description": "<p>your unique token.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "page",
            "description": "<p>number of the page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "limit",
            "description": "<p>limit of the orders for the page.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "fio",
            "description": "<p>full name of the client.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "sort",
            "description": "<p>param for sorting clients(sum/quantity/date_add).</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "version",
            "description": "<p>Current API version.</p>"
          },
          {
            "group": "Success 200",
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.client_id",
            "description": "<p>ID of the client.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.fio",
            "description": "<p>Client's FIO.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "response.total",
            "description": "<p>Total sum of client's orders.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.currency_code",
            "description": "<p>Default currency of the shop.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.quantity",
            "description": "<p>Total quantity of client's orders.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n  \"status\": true,\n  \"response\": {\n      \"clients\": [\n          {\n              \"client_id\": \"9\",\n              \"fio\": \"тестовый  заказ\",\n              \"total\": \"351.89\",\n              \"quantity\": \"2\",\n              \"currency_code\": \"UAH\"\n          },\n          {\n              \"client_id\": \"8\",\n              \"fio\": \"тестовый  заказ\",\n              \"total\": \"36.60\",\n              \"quantity\": \"1\",\n              \"currency_code\": \"UAH\"\n          }\n      ]\n  },\n  \"version\": 1\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"Error\" : \"Not one client found\",\n     \"version\": 1.0,\n     \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/clients.php",
    "groupTitle": "Clients"
  },
  {
    "type": "get",
    "url": "index.php?action=info&fc=module&module=apimodule&controller=clients",
    "title": "getClientInfo",
    "name": "getClientInfo",
    "group": "Clients",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Token",
            "optional": false,
            "field": "token",
            "description": "<p>Your unique token.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "client_id",
            "description": "<p>Unique client ID.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "version",
            "description": "<p>Current API version.</p>"
          },
          {
            "group": "Success 200",
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.client_id",
            "description": "<p>ID of the client.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.fio",
            "description": "<p>Client's FIO.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.total",
            "description": "<p>Total sum of client's orders.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.quantity",
            "description": "<p>Total quantity of client's orders.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.email",
            "description": "<p>Client's email.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.telephone",
            "description": "<p>Client's telephone.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.currency_code",
            "description": "<p>Default currency of the shop.</p>"
          },
          {
            "group": "Success 200",
            "type": "Integer",
            "optional": false,
            "field": "response.cancelled",
            "description": "<p>Total quantity of cancelled orders.</p>"
          },
          {
            "group": "Success 200",
            "type": "Integer",
            "optional": false,
            "field": "response.completed",
            "description": "<p>Total quantity of completed orders.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n  \"status\": true,\n  \"response\": {\n      \"client_id\": \"2\",\n      \"fio\": \"Test Tests\",\n      \"email\": \"y.haidai@pinta.com.ua\",\n      \"telephone\": [],\n      \"total\": \"32.39\",\n      \"quantity\": \"1\",\n      \"completed\": 0,\n      \"cancelled\": 0,\n      \"currency_code\": \"UAH\"\n  },\n  \"version\": 1\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"Error\" : \"Not one client found\",\n     \"version\": 1.0,\n     \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/clients.php",
    "groupTitle": "Clients"
  },
  {
    "type": "get",
    "url": "index.php?action=orders&fc=module&module=apimodule&controller=clients",
    "title": "getClientOrders",
    "name": "getClientOrders",
    "group": "Clients",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Token",
            "optional": false,
            "field": "token",
            "description": "<p>Your unique token.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "client_id",
            "description": "<p>Unique client ID.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "sort",
            "description": "<p>Param for sorting orders(total/date_add/completed/cancelled).</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "version",
            "description": "<p>Current API version.</p>"
          },
          {
            "group": "Success 200",
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.orders",
            "description": "<p>Array of sales orders.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.order_id",
            "description": "<p>ID of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.order_number",
            "description": "<p>Number of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.status",
            "description": "<p>Status of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.currency_code",
            "description": "<p>Default currency of the shop.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.total",
            "description": "<p>Total sum of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.date_add",
            "description": "<p>Date added of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.statuses",
            "description": "<p>Array of statuses orders.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.statuses.id_order_state",
            "description": "<p>ID of the status.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.statuses.id_lang",
            "description": "<p>Status language id.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.statuses.name",
            "description": "<p>Status name.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n        \"status\": true,\n        \"response\": {\n            \"orders\": [\n                {\n                    \"order_id\": \"6\",\n                    \"order_number\": \"6\",\n                    \"total\": \"32.39\",\n                    \"date_add\": \"2018-01-04 09:22:22\",\n                    \"currency_code\": \"UAH\",\n                    \"status\": \"5\"\n                }\n            ],\n            \"statuses\": [\n                {\n                    \"id_order_state\": \"1\",\n                    \"id_lang\": \"1\",\n                    \"name\": \"Ожидается оплата чеком\"\n                },\n                {\n                    \"id_order_state\": \"2\",\n                    \"id_lang\": \"1\",\n                    \"name\": \"Платеж принят\"\n                },\n                {\n                    \"id_order_state\": \"3\",\n                    \"id_lang\": \"1\",\n                    \"name\": \"В обработке\"\n                }\n            ]\n        },\n        \"version\": 1\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"Error\" : \"You have not specified ID\",\n     \"version\": 1.0,\n     \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/clients.php",
    "groupTitle": "Clients"
  },
  {
    "type": "get",
    "url": "/index.php?action=list&fc=module&module=apimodule&controller=orders",
    "title": "getOrders",
    "name": "GetOrders",
    "group": "Orders",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Token",
            "optional": false,
            "field": "token",
            "description": "<p>your unique token.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "page",
            "description": "<p>number of the page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "limit",
            "description": "<p>limit of the orders for the page.</p>"
          },
          {
            "group": "Parameter",
            "type": "Array[]",
            "optional": false,
            "field": "filter",
            "description": "<p>Array of the filters.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "filter.fio",
            "description": "<p>full name of the client.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "filter.order_status_id",
            "description": "<p>unique id of the order.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "filter.min_price",
            "description": "<p>min price of order.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "filter.max_price",
            "description": "<p>max price of order.</p>"
          },
          {
            "group": "Parameter",
            "type": "Date",
            "optional": false,
            "field": "filter.date_min",
            "description": "<p>min date adding of the order.</p>"
          },
          {
            "group": "Parameter",
            "type": "Date",
            "optional": false,
            "field": "filter.date_max",
            "description": "<p>max date adding of the order.</p>"
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
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.total_quantity",
            "description": "<p>Total quantity of the orders.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.currency_code",
            "description": "<p>Default currency of the shop.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "response.total_sum",
            "description": "<p>Total amount of orders.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.max_price",
            "description": "<p>Maximum order amount.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "response.api_version",
            "description": "<p>Current API version.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.orders",
            "description": "<p>Array of the orders.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.statuses",
            "description": "<p>Array of the order statuses.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.order_id",
            "description": "<p>ID of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.order_number",
            "description": "<p>Number of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.fio",
            "description": "<p>Client's FIO.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.status",
            "description": "<p>Status of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.total",
            "description": "<p>Total sum of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.date_added",
            "description": "<p>Date added of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.currency_code",
            "description": "<p>Currency of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.statuses.name",
            "description": "<p>Status Name.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.statuses.order_status_id",
            "description": "<p>Status id.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.statuses.language_id",
            "description": "<p>Language id.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n \"status\": true,\n  \"response\": {\n      \"total_quantity\": 14,\n      \"currency_code\": \"UAH\",\n      \"total_sum\": \"1257.76\",\n      \"orders\": [\n          {\n              \"order_number\": \"14\",\n              \"order_id\": \"14\",\n              \"fio\": \"тестовый  заказ\",\n              \"status\": \"Ожидается оплата чеком\",\n              \"total\": \"61.19\",\n              \"date_add\": \"2019-01-07 23:24:56\",\n              \"currency_code\": \"UAH\"\n          },\n          {\n              \"order_number\": \"4\",\n              \"order_id\": \"4\",\n              \"fio\": \"John DOE\",\n              \"status\": \"Ожидается оплата чеком\",\n              \"total\": \"89.89\",\n              \"date_add\": \"2018-01-04 09:10:54\",\n              \"currency_code\": \"UAH\"\n          }\n      ],\n      \"max_price\": \"367.19\",\n      \"statuses\": [\n          {\n              \"id_order_state\": \"1\",\n              \"id_lang\": \"1\",\n              \"name\": \"Ожидается оплата чеком\"\n          },\n          {\n              \"id_order_state\": \"2\",\n              \"id_lang\": \"1\",\n              \"name\": \"Платеж принят\"\n          }\n      ],\n  },\n  \"version\": 1,\n \"error\": \"\"\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n     \"version\": 1.0,\n     \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/orders.php",
    "groupTitle": "Orders"
  },
  {
    "type": "get",
    "url": "/index.php?action=history&fc=module&module=apimodule&controller=orders",
    "title": "getOrderHistory",
    "name": "getOrderHistory",
    "group": "Orders",
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
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.orders",
            "description": "<p>An array with information about the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.name",
            "description": "<p>Status of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.order_status_id",
            "description": "<p>ID of the status of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.date_add",
            "description": "<p>Date of adding status of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders.comment",
            "description": "<p>Some comment added from manager.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.statuses",
            "description": "<p>Statuses list for order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.statuses.name",
            "description": "<p>Status name.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.statuses.id_lang",
            "description": "<p>Language id.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.statuses.id_order_state",
            "description": "<p>Status id.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n        \"status\": true,\n        \"response\": {\n            \"orders\": [\n                {\n                    \"name\": \"Ожидается оплата чеком\",\n                    \"order_status_id\": \"1\",\n                    \"date_add\": \"2018-01-04 09:10:55\",\n                    \"comment\": \"\"\n                }\n            ],\n            \"statuses\": [\n                {\n                    \"id_order_state\": \"1\",\n                    \"id_lang\": \"1\",\n                    \"name\": \"Ожидается оплата чеком\"\n                },\n                {\n                    \"id_order_state\": \"2\",\n                    \"id_lang\": \"1\",\n                    \"name\": \"Платеж принят\"\n                },\n                {\n                    \"id_order_state\": \"3\",\n                    \"id_lang\": \"1\",\n                    \"name\": \"В обработке\"\n                }\n            ]\n        },\n        \"version\": 1\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "{\n         \"error\": \"Can not found any statuses for order with id = 5\",\n         \"version\": 1.0,\n         \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/orders.php",
    "groupTitle": "Orders"
  },
  {
    "type": "get",
    "url": "/index.php?action=info&fc=module&module=apimodule&controller=orders",
    "title": "getOrderInfo",
    "name": "getOrderInfo",
    "group": "Orders",
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
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.order_number",
            "description": "<p>Number of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.fio",
            "description": "<p>Client's FIO.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.status",
            "description": "<p>Status of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.email",
            "description": "<p>Client's email.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.telephone",
            "description": "<p>Client's phone.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.total",
            "description": "<p>Total sum of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.currency_code",
            "description": "<p>Default currency of the shop.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.date_add",
            "description": "<p>Date added of the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.statuses",
            "description": "<p>Statuses list for order.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.statuses.id_lang",
            "description": "<p>Language id</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.statuses.name",
            "description": "<p>Status name</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.statuses.id_order_state",
            "description": "<p>Status id</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n  \"status\": true,\n  \"response\": {\n      \"order_number\": 4,\n      \"fio\": \"John DOE\",\n      \"email\": \"pub@prestashop.com\",\n      \"telephone\": \"0102030405\",\n      \"date_add\": \"2018-01-04 09:10:54\",\n      \"total\": \"89.89\",\n      \"status\": \"Ожидается оплата чеком\",\n      \"statuses\": [\n          {\n              \"id_order_state\": \"1\",\n              \"id_lang\": \"1\",\n              \"name\": \"Ожидается оплата чеком\"\n          },\n          {\n              \"id_order_state\": \"2\",\n              \"id_lang\": \"1\",\n              \"name\": \"Платеж принят\"\n          }\n      ],\n      \"currency_code\": \"UAH\"\n  },\n  \"version\": 1\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n{\n  \"error\" : \"Can not found order with id = 5\",\n  \"version\": 1.0,\n  \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/orders.php",
    "groupTitle": "Orders"
  },
  {
    "type": "get",
    "url": "/index.php?action=pad&fc=module&module=apimodule&controller=orders",
    "title": "getOrderPaymentAndDelivery",
    "name": "getOrderPaymentAndDelivery",
    "group": "Orders",
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
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.payment_method",
            "description": "<p>Payment method.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.shipping_method",
            "description": "<p>Shipping method.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.shipping_address",
            "description": "<p>Shipping address.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.shipping_phone",
            "description": "<p>Shipping phone.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.payment_phone",
            "description": "<p>Payment phone.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.payment_address",
            "description": "<p>Payment address.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n  \"status\": true,\n  \"response\": {\n      \"shipping_address\": \"TEst address Dnepr \",\n      \"payment_method\": \"Payment by check\",\n      \"shipping_method\": \"Доставка завтра!\"\n      \"shipping_phone\": \"0102030405\",\n      \"payment_phone\": \"0102030405\",\n      \"payment_address\": \"TEst address Dnepr \",\n  },\n  \"version\": 1\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n {\n   \"error\": \"Can not found order with id = 90\",\n   \"version\": 1.0,\n   \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/orders.php",
    "groupTitle": "Orders"
  },
  {
    "type": "get",
    "url": "/index.php?action=products&fc=module&module=apimodule&controller=orders",
    "title": "getOrderProducts",
    "name": "getOrderProducts",
    "group": "Orders",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Token",
            "optional": false,
            "field": "token",
            "description": "<p>your unique token.</p>"
          },
          {
            "group": "Parameter",
            "type": "ID",
            "optional": false,
            "field": "order_id",
            "description": "<p>unique order id.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "version",
            "description": "<p>Current API version.</p>"
          },
          {
            "group": "Success 200",
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.products",
            "description": "<p>Array of products.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.name",
            "description": "<p>Name of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.image",
            "description": "<p>Picture of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.model",
            "description": "<p>Model of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.quantity",
            "description": "<p>Quantity of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.price",
            "description": "<p>Price of the product.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.product_id",
            "description": "<p>Unique product id.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.discount",
            "description": "<p>Percentage discount.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.discount_price",
            "description": "<p>Cost with discount.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.products.options",
            "description": "<p>Array of of the product options.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.options.option_id",
            "description": "<p>Option id.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.options.option_name",
            "description": "<p>Option name.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.options.option_value_id",
            "description": "<p>Option value id.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.options.option_value_name",
            "description": "<p>Option value name.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.products.options.language_id",
            "description": "<p>Language id of options and option values.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.total_order_price",
            "description": "<p>The array with a list of prices.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "response.total_order_price.total_discount",
            "description": "<p>The amount of the discount for the order.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "response.total_order_price.total_price",
            "description": "<p>Sum of product's prices.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "response.total_order_price.shipping_price",
            "description": "<p>Cost of the shipping.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "response.total_order_price.total",
            "description": "<p>Total order sum.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.total_order_price.currency_code",
            "description": "<p>Currency of the order.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK  {\n  \"status\": true,\n  \"response\": {\n      \"products\": [\n          {\n              \"image\": \"http://prestashop1721.pixy.pro/1-home_default/.jpg\",\n              \"name\": \"Faded Short Sleeve T-shirts - Color : Orange, Size : S\",\n              \"model\": \"\",\n              \"quantity\": 1,\n              \"price\": \"16.51\",\n              \"product_id\": \"1\",\n              \"discount_price\": \"0.000000\",\n              \"discount\": \"0\"\n          }\n      ],\n      \"total_order_price\": {\n          \"total_discount\": 47.38,\n          \"total_price\": \"89.89\",\n          \"shipping_price\": 2,\n          \"total\": \"91.89\",\n          \"currency_code\": \"UAH\"\n      }\n  },\n  \"version\": 1\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n{\n     \"error\": \"Can not found any products in order with id = 10\",\n     \"version\": 1.0,\n     \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/orders.php",
    "groupTitle": "Orders"
  },
  {
    "type": "get",
    "url": "index.php?action=delivery_update&fc=module&module=apimodule&controller=orders",
    "title": "changeOrderDelivery",
    "name": "update_Order_Delivery",
    "group": "Orders",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "address",
            "description": "<p>New shipping address.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "city",
            "description": "<p>New shipping city.</p>"
          },
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
            "type": "Boolean",
            "optional": false,
            "field": "response",
            "description": "<p>Status of change address.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "  HTTP/1.1 200 OK\n{\n      \"status\": true,\n      \"version\": 1.0\n }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n{\n  \"error\": \"Can not change address\",\n  \"version\": 1.0,\n  \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/orders.php",
    "groupTitle": "Orders"
  },
  {
    "type": "get",
    "url": "index.php?action=status_update&fc=module&module=apimodule&controller=orders",
    "title": "statusUpdate",
    "name": "update_Order_Status",
    "group": "Orders",
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
            "type": "Number",
            "optional": false,
            "field": "status_id",
            "description": "<p>unique status ID.</p>"
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
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.name",
            "description": "<p>Status name.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.date_add",
            "description": "<p>Date of change.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n \"status\": true,\n  \"response\": {\n      \"name\": \"Платеж принят\",\n      \"date_add\": \"2019-01-14 13:21:02\"\n  },\n  \"version\": 1\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n {\n   \"error\": \"Can not found order with id = 90\",\n   \"version\": 1.0,\n   \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/orders.php",
    "groupTitle": "Orders"
  },
  {
    "type": "get",
    "url": "/index.php?fc=module&module=apimodule&controller=statistic",
    "title": "getDashboardStatistic",
    "name": "getDashboardStatistic",
    "group": "Statistic",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "filter",
            "description": "<p>Period for filter(day/week/month/year).</p>"
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
            "type": "Array[]",
            "optional": false,
            "field": "response",
            "description": "<p>Array with content response.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "version",
            "description": "<p>Current API version.</p>"
          },
          {
            "group": "Success 200",
            "type": "Bool",
            "optional": false,
            "field": "status",
            "description": "<p>Response status.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.xAxis",
            "description": "<p>Period of the selected filter.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.clients",
            "description": "<p>Clients for the selected period.</p>"
          },
          {
            "group": "Success 200",
            "type": "Array[]",
            "optional": false,
            "field": "response.orders",
            "description": "<p>Orders for the selected period.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.currency_code",
            "description": "<p>Default currency of the shop.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "response.total_sales",
            "description": "<p>Sum of sales of the shop.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.sale_year_total",
            "description": "<p>Sum of sales of the current year.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.orders_total",
            "description": "<p>Total orders of the shop.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "response.clients_total",
            "description": "<p>Total clients of the shop.</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK {\n          \"response\": {\n              \"xAxis\": [\n                 1,\n                 2,\n                 3,\n                 4,\n                 5,\n                 6,\n                 7\n             ],\n             \"clients\": [\n                 0,\n                 0,\n                 0,\n                 0,\n                 0,\n                 0,\n                 0\n             ],\n             \"orders\": [\n                 1,\n                 0,\n                 0,\n                 0,\n                 0,\n                 0,\n                 0\n             ],\n             \"total_sales\": \"1920.00\",\n             \"sale_year_total\": \"305.00\",\n             \"currency_code\": \"UAH\",\n             \"orders_total\": \"4\",\n             \"clients_total\": \"3\"\n          },\n          \"status\": true,\n          \"version\": 1.0\n }",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "\n{\n  \"error\": \"Unknown filter set\",\n  \"version\": 1.0,\n  \"Status\" : false\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "apimodule/controllers/front/statistic.php",
    "groupTitle": "Statistic"
  }
] });
