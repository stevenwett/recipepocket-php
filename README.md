# Recipepocket App
A free and simple way to store your recipes.

## Auth Endpoints
This site is using [WP Firebase Auth](https://github.com/stevenwett/wp-firebase-auth) to manage user authentication with Firebase.

## User Endpoints
### Create User
POST `/wp-json/recipepocket/v1/user`

Request body:
```json
{
	"email": "",
	"first_name": "",
	"last_name": ""
}
```
* `email` and `first_name` are required.

Response body:
```json
{
	"message": ""
}
```
* 201: Created user.
* 400: Bad request.

### Update User
PATCH `/wp-json/recipepocket/v1/user`
* Must be authenticated.

Request body:
```json
{
	"user_id": 0,
	"firebase_uid": "",
	"email": "",
	"first_name": "",
	"last_name": ""
}
```
* `user_id` is required.
* `firebase_uid` is required in order to change `email`.

Response body:
```json
{
	"message": "",
	"user": {
		"id": 0,
		"active": 1,
		"user_id": 0,
		"name": "",
		"source": {},
		"author": "",
		"preparation_steps": [],
		"ingredients": [],
		"created_gmt": "",
		"modified_gmt": ""
	}
}
```
* 200: Updated user.
* 400: Bad request.

### Delete User
DELETE `/wp-json/recipepocket/v1/user/?user_id=0`
* `user_id` is required.
* Must be authenticated.
* Deactivates, does not delete.

Response body:
```json
{
	"message": ""
}
```
* 200: Deleted user.
* 400: Bad request.

## Recipe Endpoints

### Create Recipe
POST `/wp-json/recipepocket/v1/recipe`
* Must be authenticated.

Request body:
```json
{
	"user_id": 0,
	"name": "",
	"author": "",
	"source": {
		"name": "",
		"link": ""
	},
	"preparation_steps": [
		{
			"title": "",
			"description": ""
		}
	],
	"ingredients": [
		{
			"quantity": 0,
			"units": "",
			"name": ""
		}
	]
}
```
* `user_id`, `name`, `preparation_steps`, `ingredients` are required.

Response body:
```json
{
	"message": ""
}
```
* 201: Created recipe.
* 400: Bad request.

### Get Recipe
GET `/wp-json/recipepocket/v1/recipe/?recipe_id=0&hash=123abc`
* `recipe_id` is required.
* User must either be authenticated or a valid `hash` must be provided.

Response body:
```json
{
	"message": "",
	"recipe": {
		"id": 0,
		"active": 1,
		"user_id": 0,
		"name": "",
		"author": "",
		"source": {
			"name": "",
			"link": ""
		},
		"preparation_steps": [
			{
				"title": "",
				"description": ""
			}
		],
		"ingredients": [
			{
				"quantity": 0,
				"units": "",
				"name": ""
			}
		]
	}
}
```
* 201: Created recipe.
* 400: Bad request.

### Update Recipe
PATCH `/wp-json/recipepocket/v1/recipe`
* Must be authenticated.
* Can only be used if `user_id` found in the requested recipe matches the authenticated user's id.

Request body:
```json
{
	"recipe_id": 0,
	"active": 1,
	"name": "",
	"author": "",
	"source": {
		"name": "",
		"link": ""
	},
	"preparation_steps": [
		{
			"title": "",
			"description": ""
		}
	],
	"ingredients": [
		{
			"quantity": 0,
			"units": "",
			"name": ""
		}
	]
}
```
* `recipe_id` is required.

Response body:
```json
{
	"message": ""
}
```
* 200: Updated recipe.
* 400: Bad request.


### Delete Recipe
DELETE `/wp-json/recipepocket/v1/recipe/?recipe_id=0`
* `recipe_id` is required.
* Must be authenticated.
* Deactivates, does not delete.
* Can only be used if `user_id` in this recipe matches the authenticated user's id.

Response body:
```json
{
	"message": ""
}
```
* 200: Deleted recipe.
* 400: Bad request.
