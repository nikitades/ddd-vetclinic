# It's a pet project to try the DDD approach in battle

## The idea of the project is to make a patients tracking system for the imaginary vet clinic

### The requirements:
- We (the clinic) want to register all the incoming patients
- We want to attach every patient to a doctor
- A doctor can have not more than 3 patients simultaneously
- The pet's owner should be allowed to check the pet's status via the API (a cool pet's owner with Postman)
- The pet's owner should be able to leave the email to get the notification as soon as the pet is released
- We want to release the patients when the treatment is done

### The additional requirements:
- We (the clinic) want to be able to check the patient's status via CLI command

- - -

### The abstract domain model

* A patient

```
Id: int //Doctrine
Name: string
Species: string
BirthDate: DateTime
Cards: Card[]
Owner: Owner
```

* A card

```
Id: int
Patient: Patient
Cases: Case[]
CreatedAt: DateTime
```

* An owner

```
Id: int
Name: string
Phone: string
Address: string
Email: string
NotificationRequired: bool
Patients: Patient[]
RegisterdAt: DateTime
```

* A case

```
Id: int
Description: string
Treatment: string
Card: Card
StartedAt: DateTime
Ended: bool
EndedAt: DateTime
```

Since the patient can be created without the owner, the patient appears to be an aggregate root here.
So all the persistence methods regarding the patient, card, medical case or owner are localed in the patients repository (IPatientRepository).

- - -

### The idea

It's a system designed with a hexagonal architecture in mind.
I tried to separate the domain model from the application layer, and the application layer from the infrastructure services.

The concepts:
- domain level (domain models and their relations) //not a doctrine models!
- application level - services making actions on domain models
- infrastructure level
    - dbal-agnostic repository (IPatientRepository) - contains all the methods to persist the data, but could rearranged to use any persistence layer you with to
    - framework repositories - doctrine repositories containing the queries
    - framework http layer - serving as an interface with incoming and outcoming data
    - framework commands layer - another interface providing some control over the application services

- - -

### Routes to implement

    Public:
    V   - GET /patients/state
    V   - GET /patients/requireNotification

    Admin: 
    V   - POST /patients
    V   - POST /owners
    V   - PUT /patients/:id/attachToOwner
        - PUT /patients/released
        - GET /patients/onTreatment
        - POST /patients/:id/cards/:cardId -> createMedicalCase
        - GET /patients/:id/cards -> getCards
        - POST /patients/:id/cards -> createCard
        - PUT /patients/:id/cards -> updateCard
        - DELETE /patients/:id -> deleteCard
        - GET /patients/:id/cards/:cardId -> getCard
        - GET /patients/:id/cards/:cardId/cases -> getCardCases
        - PUT /patients/:id/cards/:cardId/cases/:caseId -> updateMedicalCase
        - DELETE /patients/:id/cards/:cardId/cases/:caseId -> deleteMedicalCase
        - GET /patients/:id/cases -> getPatientCases
        - GET /doctors/available
        - GET /doctors/all

It takes too long to implement all the methods covered with all the tests, so I created three endpoints. Just imagine the rest of them, they are not planned to differ much.

- - -

### CLI Commands

    clinic:patient:state <patientId/patientName>

Located at the infrastucture level, belonging to the framework-bound part of the system, this command is just another way to call the Patient service methods.