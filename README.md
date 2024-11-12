<img src="docs/assets/logo_long.png" alt="Logo du script 'JWM'" width="25%"/>

# [J-BAUER][EN] Jo Windows Manager : PHP API Add-on
**J.W.M.-PHP** is a add-on for **J.W.M.** that adds the hability to fetch window data from a server using an API written in PHP.
Designed to be easy to use, it allows you to request a window with a single command.
If needed, you can customize its behavior by setting various options.</br>
(WARNING: for this script to work you need [**J.W.M.**](https://github.com/jo57430/Web-JWM/) !)

> **By Jonathan BAUER (J-BAUER)**</br>
> Version: 1.0.0</br>
> Date: 12/11/2024</br>
> License: Apache 2.0</br>
> Requirement: [**J.W.M.** Ver: 1.0.0](https://github.com/jo57430/Web-JWM/)

## How Does It Work ?
The system operates in three main part:
1. **(ServerSide) Add you window to the configuration file of the API**: To start with, add to the window configuration, so that the API knows what to send.
2. **(ClientSide) Set the API localization**: Using the `JWM.setApiPath()`function, set the url of the API.
3. **(ClientSide) Request a window**: Call the `JWM.requestWindow()` function to create a window with the result of the api respond.

## How to Install It
### Default method
Installing J.W.M.-PHP is straightforward. Simply add the following line in the `<head>` section of your page, after the J.W.M. JavaScript files:
```html
<script src="https://raw.githack.com/jo57430/Web-JWM/refs/heads/master/lib/jwm_1-0-0.js"></script> 
<!-- <----- Add all the below line after this one !  -->

<script src="<Your link>/jwm-php_1.0.0.js"></script>
```
Then copy the API folder to where you went to store the API.</br>
That's all you need to do!

## Documentation

### API Config File
```json
{
    "loadFromFolder": "", // Used to define a folder that is scanned to find individual menu config files. (example: a MyMenu.json file > this file will be read and added to the list of available menus under the id: ‘MyMenu’)
    "loadFromFolders": [], // Same has on top but for multiple folder.

    "pagesList":{ // The list of all menu in addition to those found in the files above.
        "exempleMenu":{ // The menu id as is key. (The same id used on the client side to request this menu)
            "title": "Test Page !", // The menu title.
            "contentType": 1,       // The type of content is this menu. (0 = raw data inside the 'content' key, 1 = htmlfile to open and send it's content, 2 = phpfile to execute and send it's output, 3 = A URL to open inside a <iframe>)
            "content": "./../pages/test.html", // The data or url depending on the type of content (contentType).

            "options": { // The window option. (Look at J.W.M. for a list of available option)
                "width": 300,
                "height": 150
            },

        }
    },

    "aliasList":{ // A list of window id alias.
        "alias": "exempleMenu"
    }
}
```
### Individual Menu Configuration File.
This file is used to define a menu, stored in a folders pointed to by the ‘loadFromFolder’ configuration.
The name of the file is used has the menu id.
```json
    "title": "Test Page 2 !", // The menu title.
    "contentType": 1,  // The type of content is this menu. (0 = raw data inside the 'content' key, 1 = htmlfile to open and send it's content, 2 = phpfile to execute and send it's output, 3 = A URL to open inside a <iframe>)
    "content": "./../pages/test.html",  // The data or url depending on the type of content (contentType).

    "options": {  // The window option. (Look at J.W.M. for a list of available option)
        "width": 300,
        "height": 150
    },

    "aliasList":[ // A list of window id alias.
        "test2Page"
    ],
```
### Global Function

#### JWM.setApiPath()
**Description**
Used to set the path to the API.

**Arguments**
- `str_url` {string} : The url to the API.

#### [ASYNC] JWM.requestWindow() 
**Description**
Used to fetch and open a new window using the API.

**Arguments**
- `str_url` {string} : Used to fetch and open a new window using the API.
- `str_identifier` {string} : [OPTIONAL:(str_id)_(w_nextID)] The unique identifier of this window.
- `obj_options` {objet} : [OPTIONAL:{}] Used to personalize/overide some of the behaviour of this window.

**Return**
{JWM_Window} Return the window object in a promise.

## Example
Below is an example of how to request a window :
```javascript
// Step 1: Create an instance of JWM_WindowManager
const windowManager = new JWM_WindowManager();

// Step 2: Set the API URL
JWM.setApiPath("<path to the API>");

// Step 3: Request the window and open it.
const myWindow = await JWM.requestWindow("exempleMenu");
```