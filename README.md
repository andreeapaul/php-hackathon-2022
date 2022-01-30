# PHP Hackathon
This document has the purpose of summarizing the main functionalities your application managed to achieve from a technical perspective. Feel free to extend this template to meet your needs and also choose any approach you want for documenting your solution.

## Problem statement
*Congratulations, you have been chosen to handle the new client that has just signed up with us.  You are part of the software engineering team that has to build a solution for the new client’s business.
Now let’s see what this business is about: the client’s idea is to build a health center platform (the building is already there) that allows the booking of sport programmes (pilates, kangoo jumps), from here referred to simply as programmes. The main difference from her competitors is that she wants to make them accessible through other applications that already have a user base, such as maybe Facebook, Strava, Suunto or any custom application that wants to encourage their users to practice sport. This means they need to be able to integrate our client’s product into their own.
The team has decided that the best solution would be a REST API that could be integrated by those other platforms and that the application does not need a dedicated frontend (no html, css, yeeey!). After an initial discussion with the client, you know that the main responsibility of the API is to allow users to register to an existing programme and allow admins to create and delete programmes.
When creating programmes, admins need to provide a time interval (starting date and time and ending date and time), a maximum number of allowed participants (users that have registered to the programme) and a room in which the programme will take place.
Programmes need to be assigned a room within the health center. Each room can facilitate one or more programme types. The list of rooms and programme types can be fixed, with no possibility to add rooms or new types in the system. The api does not need to support CRUD operations on them.
All the programmes in the health center need to fully fit inside the daily schedule. This means that the same room cannot be used at the same time for separate programmes (a.k.a two programmes cannot use the same room at the same time). Also the same user cannot register to more than one programme in the same time interval (if kangoo jumps takes place from 10 to 12, she cannot participate in pilates from 11 to 13) even if the programmes are in different rooms. You also need to make sure that a user does not register to programmes that exceed the number of allowed maximum users.
Authentication is not an issue. It’s not required for users, as they can be registered into the system only with the (valid!) CNP. A list of admins can be hardcoded in the system and each can have a random string token that they would need to send as a request header in order for the application to know that specific request was made by an admin and the api was not abused by a bad actor. (for the purpose of this exercise, we won’t focus on security, but be aware this is a bad solution, do not try in production!)
You have estimated it takes 4 weeks to build this solution. You have 3 days. Good luck!*

## Technical documentation
### Data and Domain model
In this section, please describe the main entities you managed to identify, the relationships between them and how you mapped them in the database.

For the implementation of the solution I chose to create four tables in the database: admins, programmes, bookings and rooms. There is a one to many relationship between the rooms table and the programmes table, so the programmes table contains a foreign roomID key. In addition, the program table contains data on the beginning, end, and maximum number of participants allowed in a programme. 
There is a one-to-many relationship between the bookings table and the programmes table, because more users can register for each program. Thus, in the bookings table we can find the person's CNP and programmeID.

In the admins table we find a list of admins, each with a random token in the random_token field so that the application knows if the request was sent by an admin or not.

### Application architecture
In this section, please provide a brief overview of the design of your application and highlight the main components and the interaction between them.

The application was made using the CakePHP framework, thus having an MVC structure.
The Controllers act as an interface between Model and View components to process all the business logic and incoming requests, manipulate data using the Model component and interact with the Views to render the final output. At this time, all methods used in the application are defined in the ProgrammesController. 
The Model component corresponds to all the data-related logic that the user works with. This can represent either the data that is being transferred between the View and Controller components or any other business logic-related data.
The View component is used for all the UI logic of the application. This part will be developed in the future, at this moment the application does not have a graphical interface.

###  Implementation
##### Functionalities
For each of the following functionalities, please tick the box if you implemented it and describe its input and output in your application:

[x] Brew coffee \
[x] Create programme \
[x] Delete programme \
[x] Book a programme 

##### Business rules
Please highlight all the validations and mechanisms you identified as necessary in order to avoid inconsistent states and apply the business logic in your application.

The validations that have been made are:
- The CNP entered by the user when registering for a programme must comply with the rules for forming a CNP in Romania;
- the same user cannot register to more than one programme in the same time interval;
- the same room cannot be used at the same time for separate programmes;
- a program can only be created or deleted by an admin, represented by a specific random token;
- an user cannot register for a program that has already reached the maximum number of participants allowed.


##### 3rd party libraries (if applicable)
Please give a brief review of the 3rd party libraries you used and how/ why you've integrated them into your project.

##### Environment
Please fill in the following table with the technologies you used in order to work at your application. Feel free to add more rows if you want us to know about anything else you used.
| Name | Choice |
| ------ | ------ |
| Operating system (OS) | e.g. macOS Big Sur|
| Database  | e.g. MySQL 5.7.37|
| PHP | e.g. 7.4.21 |
| Framework CakePHP| e.g. 3.10.2 |
| IDE | e.g. Visual Studio Code |

### Testing
In this section, please list the steps and/ or tools you've used in order to test the behaviour of your solution.

I used Postman in order to test the solution. I created one feature at a time, then tested it to see if it works properly and to correct any errors.


## Feedback
In this section, please let us know what is your opinion about this experience and how we can improve it:

1. Have you ever been involved in a similar experience? If so, how was this one different?
    I have never been involved in such an experience.
2. Do you think this type of selection process is suitable for you?
    I think yes, in this way both sides, both me and the employer can figure out if I live up to expectations for this role.
3. What's your opinion about the complexity of the requirements?
    I consider that the requirement was of medium complexity and was suitable for an employment test.
4. What did you enjoy the most?
    So far, I haven't had a chance to work with REST API, and this way I've researched more and practiced this topic.
5. What was the most challenging part of this anti hackathon?
    To work with API, because this is the first time I have the opportunity to write code for an REST API.
6. Do you think the time limit was suitable for the requirements?
   Yes, I believe that the requirement could be resolved within the allotted time. 
7. Did you find the resources you were sent on your email useful?
    Yes.
8. Is there anything you would like to improve to your current implementation?
    Yes, at this moment I am in the exam session and I did not manage to allocate enough time so that the code respects all the rules of good quality code.
9. What would you change regarding this anti hackathon?

