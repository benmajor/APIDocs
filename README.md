# APIDocs

This is a simple PHP tool for documenting APIs without any of the hassle that typically comes with it. I have created this mainly out of frustration for the lack of tools already existing in the tech world. It's probably not the most flexible or robust system around, but it certainly does the job.

The system uses YAML files and Markdown, along with Twig templates for easy modification and customisation. Please feel free to download and use within your own projects *without warranty*.

### Adding content:

Defining sections is easy, and we use YAML files to add sections and sub-categories to the documentation. These files should be placed in the `content/` directory by default, but this can be changed by modifying by changing the various settings within `config.yaml`. 

A sample `content/` directory would look something like the following:

```
1-introduction.yaml
2-authentication.yaml
3-errors.yaml
4-user/
  0-introduction.yaml
  1-register.yaml
  2-login.yaml
```

Sections that will contain subsections should be placed inside a directory within the content directory, and a file using the `0` as its first name should be present to contain information about the section. 

The YAML file will look something like this:

```yaml
title: Register a User Account
content: register.yaml
request:
  method: post
  endpoint: /users/
  params:
    forename*: 
      - John
      - description
    surname*: 
      - Doe
      - description2
    email*: 
      - johndoe@example.com
      - email address of the user
    password1*: 
      - password123
      - password to be used for user **authentication**
    password2*: 
      - password123
      - confirmation of user password

response:
  statusCode: 201
  params:
    uid:
      - 1
      - The unique ID of the created user.
    user:
      - Object containing the information for the created user.
      - name: 
        - John
        - The forename of the user
        login:
        - Object containing login information about the user
        - id:
          - 1
          - The ID of the user login
          name:
          - Test
          - A random name for the session
```

The system will automatically parse the file and output the necessary markup and code. Notice that `content` can either be a pointer to a valid Markdown file, or a Markdown-encoded string. 