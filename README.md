# Ticket Management System &rlarr; tms
Ticket Management System or as abbreviated _tms_ is a web application that is a core helpdesk application. It (when finished) allows managing tickets, projects, clients and users.

The project is currently in version: _0.0.1-dev_ and is still under heavy development. So the application might change it's design and/or behavior.

## Tech stack
It is mainly written in PHP but it uses third party components like Bootstrap, jQuery and PHPMailer. It utilizes AJAX (using jQuery) and thus JavaScript is used for more dynamic behavior.

## Developer guide / Project code description
The entry point of the application is `index.php` that loads core scripts and runs the application. No other script is being used from client-side.

On the other hand are the server-side scripts that are located in the `app/` directory.

### App directory tree
 - ajax &rarr; Ajax scripts
 - authenticators &rarr; Authentication scripts
 - authorizators &rarr; Authorization scripts
 - components &rarr; Component scripts
 - core &rarr; Core scripts
    - db &rarr; Database connection and query handling scripts
        - querybuilder &rarr; My own QueryBuilder library
    - logger &rarr; Logging library
    - vendor &rarr; Vendor libraries and components
- entities &rarr; Entity classes
- enums &rarr; Enum classes
- exceptions &rarr; Custom exception definitions
- helpers &rarr; Helper classes
- models &rarr; Database models
    - not used
- modules &rarr; UI definition classes
- panels &rarr; Toppanel (navigation bar) definitions
    - not used, will be removed
- repositories &rarr; Database repositories
- services &rarr; Service classes
    - not used, will be used later
- templates &rarr; Non-UI templates
    - not used, will be used later
- ui &rarr; UI components
- widgets &rarr; Widget components
    - not used, will be removed

Every directory specified above will have its own `README.md` file (will be added in future) that will contain information on what to put there and put to not put there.

There are also directories like `logs/` that contains log files, `cache/` that contains cached files, `img/` that contains images, `js` that contains JavaScript source code files and `css` that contains CSS style files.