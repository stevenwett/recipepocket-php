# Recipepocket App
A free and simple way to store your recipes.

\* Asterisks mean you must be authenticated to use this endpoint.

## Auth Endpoints
This site is using [WP Firebase Auth](https://github.com/stevenwett/wp-firebase-auth) to manage user authentication with Firebase.

## User Endpoints
### Create User: POST `/wp-json/recipepocket/v1/user`

```
{
	email: '',
	first_name: '',
	last_name: ''
}
```
`email` and `first_name` are required.

### Update User\*: PATCH `/wp-json/recipepocket/v1/user`

```
{
	user_id: 0,
	firebase_uid: '',
	email: '',
	first_name: '',
	last_name: '',
}
```
`user_id` is required. `firebase_uid` is required in order to change `email`.

### Delete User\*: DELETE `/wp-json/recipepocket/v1/user`

```
{
	user_id: 0
}
```
`user_id` is required. Deactivates, does not delete.

## Recipe Endpoints

### Create Recipe\*: POST `/wp-json/recipepocket/v1/recipe`

```
{
	user_id: 0,
	name: '',
	author: '',
	source: {
		name: '',
		link: '',
	},
	preparation_steps: [
		{
			title: '',
			description: '',
		}
	],
	ingredients: [
		{
			quantity: 0,
			units: '',
			name: '',
		}
	]
}
```
`user_id`, `name`, `preparation_steps`, `ingredients` are required.

### Get Recipe\*: GET `/wp-json/recipepocket/v1/recipe`

```
{
	recipe_id: 0
}
```
`recipe_id` is required.

### Update Recipe\*: PATCH `/wp-json/recipepocket/v1/recipe`

```
{
	recipe_id: 0,
	active: 1,
	name: '',
	author: '',
	source: {
		name: '',
		link: '',
	},
	preparation_steps: [
		{
			title: '',
			description: '',
		}
	],
	ingredients: [
		{
			quantity: 0,
			units: '',
			name: '',
		}
	]
}
```
`recipe_id` is required.

### Delete Recipe\*: DELETE `/wp-json/recipepocket/v1/recipe`

```
{
	recipe_id: 0
}
```
`recipe_id` is required. Deactivates, does not delete. Can only be used if `user_id` in this recipe matches the authenticated user's id.
