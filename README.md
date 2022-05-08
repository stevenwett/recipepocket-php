# Recipepocket App
A free and simple way to store your recipes.

## Auth Endpoints
This site is using [WP Firebase Auth](https://github.com/stevenwett/wp-firebase-auth) to manage user authentication with Firebase.

## User Endpoints
### Create User
POST `/wp-json/recipepocket/v1/user`

#### Request Body
```json
{
	"email": "",
	"first_name": "",
	"last_name": ""
}
```
* `email` and `first_name` are required

#### Response
```json
{
	"message": ""
}
```
* 201: Created user
* 400: Bad request

### Update User
PATCH `/wp-json/recipepocket/v1/user`

#### Request Body
```json
{
	"user_id": 0,
	"firebase_uid": "",
	"email": "",
	"first_name": "",
	"last_name": ""
}
```
* Must be authenticated
* `user_id` is required
* `firebase_uid` is required in order to change `email`
* `user_id` must match with authenticated user's id

#### Response
* 200: Updated user
* 400: Bad request
```json
{
	"message": ""
}
```

### Delete User
DELETE `/wp-json/recipepocket/v1/user?user_id=0`
* Must be authenticated
* `user_id` is required
* Deactivates, does not delete
* `user_id` must match with authenticated user's id

#### Response
```json
{
	"message": ""
}
```
* 200: Deleted user
* 400: Bad request

## Recipe Endpoints

### Create Recipe
POST `/wp-json/recipepocket/v1/recipe`

#### Request Body
```json
{
	"user_id": 0,
	"name": "",
	"description": "",
	"author": "",
	"source": {
		"name": "",
		"link": ""
	},
	"images": [
		{
			"link": "",
			"alt": ""
		}
	],
	"reviews": [
		{
			"name": "",
			"review": "",
			"rating": {
				"value": 0,
				"count": 0
			},
			"datetime": ""
		}
	],
	"rating": {
		"value": 0,
		"count": 0,
		"worst": 1,
		"best": 5
	},
	"yield": {
		"quantity": 0,
		"units": "",
		"name": ""
	},
	"time": {
		"prep": 0,
		"cook": 0,
		"total": 0
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
* Must be authenticated
* `user_id` must match with authenticated user's id
* `user_id`, `name`, `preparation_steps`, `ingredients` are required

#### Response
```json
{
	"message": ""
}
```
* 201: Created recipe
* 400: Bad request

### Get Recipe
GET `/wp-json/recipepocket/v1/recipe?recipe_id=0&hash=123abc`
* `recipe_id` is required
* User must either be authenticated or a valid `hash` must be provided

#### Response
* 200: Got recipe
* 400: Bad request
```json
{
	"message": "",
	"recipe": {
		"id": 0,
		"active": 1,
		"user_id": 0,
		"name": "",
		"description": "",
		"author": "",
		"source": {
			"name": "",
			"link": ""
		},
		"images": [
			{
				"link": "",
				"alt": ""
			}
		],
		"rating": {
			"value": 0,
			"count": 0,
			"worst": 1,
			"best": 5
		},
		"yield": {
			"quantity": 0,
			"units": "",
			"name": ""
		},
		"time": {
			"prep": 0,
			"cook": 0,
			"total": 0
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
		],
		"nutrition": {}
	}
}
```

### Update Recipe
PATCH `/wp-json/recipepocket/v1/recipe`

#### Request Body
```json
{
	"recipe_id": 0,
	"active": 1,
	"name": "",
	"description": "",
	"author": "",
	"source": {
		"name": "",
		"link": ""
	},
	"images": [
		{
			"link": "",
			"alt": ""
		}
	],
	"rating": {
		"value": 0,
		"count": 0,
		"worst": 1,
		"best": 5
	},
	"yield": {
		"quantity": 0,
		"units": "",
		"name": ""
	},
	"time": {
		"prep": 0,
		"cook": 0,
		"total": 0
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
	],
	"nutrition": {}
}
```
* Must be authenticated
* `recipe_id` is required
* Can only be used if `user_id` found in the requested recipe matches the authenticated user's id

#### Response
```json
{
	"message": ""
}
```
* 200: Updated recipe
* 400: Bad request

### Delete Recipe
DELETE `/wp-json/recipepocket/v1/recipe?recipe_id=0`
* Must be authenticated
* `recipe_id` is required
* Deactivates, does not delete
* Can only be used if `user_id` in this recipe matches the authenticated user's id

#### Response
```json
{
	"message": ""
}
```
* 200: Deleted recipe
* 400: Bad request

## Review Endpoints

### Add Review
POST `/wp-json/recipepocket/v1/recipe/review`

#### Request Body
```json
{
	"recipe_id": 0,
	"author": "",
	"review": "",
	"rating": {
		"value": 0,
		"count": 0,
		"worst": 1,
		"best": 5
	}
}
```
* Must be authenticated
* `recipe_id` is required

#### Response
```json
{
	"message": "",
	"review": {}
}
```
* 200: Review added
* 400: Bad request

### Update Review
PATCH `/wp-json/recipepocket/v1/recipe/review`

#### Request Body
```json
{
	"review_id": 0,
	"author": "",
	"review": "",
	"rating": {
		"value": 0,
		"count": 0,
		"worst": 1,
		"best": 5
	}
}
```
* Must be authenticated
* Can update review if `user_id` on review matches authenticated user's id, or
* Can update review if `user_id` on recipe matches authenticated user's id

#### Response
```json
{
	"message": ""
}
```
* 200: Review updated
* 400: Bad request

### Delete Review
DELETE `/wp-json/recipepocket/v1/recipe/review?review_id=0`

#### Response
```json
{
	"message": ""
}
```
* 200: Review deleted
* 400: Bad request
