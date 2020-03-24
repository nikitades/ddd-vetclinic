# It's a pet project to try the DDD approach in battle

## The idea of the project is to make a patients tracking system for the imaginary vet clinic

### The requirements:
- We (the clinic) want to register all the incoming patients
- We want to attach every patient to a doctor
- A doctor can have not more than 3 patients simultaneously
- The pet's owner should be allowed to check the pet's status via the API (a cool pet's owner with Postman)
- The pet's owner should be able to leave the email to get the notification as soon as the pet is released
- We want to release the patients when the treatment is done

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