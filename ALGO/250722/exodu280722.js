// Create an object:
//const eleve = {type:"sérieux", genre:"male", age:"22 ans"};

// Display some data from the object:
//document.getElementById("demo").innerHTML = "le type de l'eleve est " + eleve.type
//document.getElementById("demo2").innerHTML = "le genre de l'eleve est " + eleve.genre
//document.getElementById("demo3").innerHTML = "l'age de l'eleve   est " + eleve.age


const person = {
    firstName: "Robert",
    lastName : "Michel",
    id       : 5566,
    fullName : function() {
      return this.firstName + " " + this.lastName + " " +this.id;
    }
  };
  document.getElementById("demo").innerHTML= person.fullName();
