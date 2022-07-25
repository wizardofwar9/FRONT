/*document.querySelector('h4').style .background= "blue";*/
/*injecte dans le document h4 une methode  qui change le style ici la couleur*/

/*const baliseHtml = document.querySelector("h4");

console.log(baliseHtml);

baliseHtml.style.color="red";
definit un nom en const donc peut etre appelée par son nom h4 + la method 
selectionne la balise h4 et lui donne la couleur rouge*/

/*click events*/
const questionContainer = document.querySelector(".click-event");
/*console.log(".click-event");
questionContainer.style.borderRadius = "150px";*/
const btn1 = document.querySelector("#btn-1");
const btn2 = document.getElementById("btn-2");
const response = document.querySelector("p");

console.log(response);

console.log(btn1, btn2);

questionContainer.addEventListener("click", () => {
    // questionContainer.style.background = "purple";
    questionContainer.classList.toggle("question-clicked")
});
btn1.addEventListener('click', () => {
    //response.style.visibility = "visible";
    response.classList.add("show-response");
    response.style.background = "green";

});

btn2.addEventListener('click', () => {
    //response.style.visibility = "visible"
    response.classList.add("show-response");
    response.style.background = "red";

});

//priorité des classes <div></div>  > #id > .class >  balise html
//sinon dans le css rajouter par exemple pink !important
//--------------------------------------------------------------
//Mouse Events
//const mousemove = document.querySelector(".mousemove");

//console.log(mousemove);
//window.addEventListener("mousemove", (e) => {
//console.log(e.target);

//});
const mousemove = document.querySelector(".mousemove");

//console.log(mousemove);
window.addEventListener("mousemove", (e) => {
    mousemove.style.left = e.pageX + "px";
    mousemove.style.top = e.pageY + "px";

});

window.addEventListener("mousedown", () => {
    mousemove.style.transform = "Scale(2) translate(-25%, -25%)";
});

window.addEventListener("mouseup", () => {
    mousemove.style.transform = "Scale(1) translate(-50%, -50%)";
    mousemove.style.border = "2px solid green";
});

questionContainer.addEventListener("mouseenter", () => {
    questionContainer.style.background = "rgba(0,0,0,0.6)";

});
questionContainer.addEventListener("mouseout", () => {
    questionContainer.style.background = "orange";

});

response.addEventListener("mouseover", () => {
    response.style.transform = "rotate(2deg)";
});
//--------------------------------------------------------------------
//Keypress events
const KeypressContainer = document.querySelector(".keypress");
const key = document.getElementById("key");
console.log(key);
const ring = () => {
    const audio = new Audio();
    audio.src = "son.mp3/Enter.mp3";
    audio.play();
}

document.addEventListener("keypress", (e) => {
    //console.log("super !");
    //console.log(e.key);
    key.textContent = e.key;

    if (e.key === "j") {
        KeypressContainer.style.background = "pink";
    } else if (e.key === "l") {
        KeypressContainer.style.background = "green";
    } else {
        KeypressContainer.style.background = "red";
    }
    ring();
});
//--------------------------------------------
//scroll event
const nav = document.querySelector("nav");
console.log(nav);

window.addEventListener("scroll", () => {
    /*console.log(window.scrollY);*/
    if (window.scrollY > 120) {
        nav.style.top = 0;
    } else {
        nav.style.top = "-50px"
    }
})
//----------------------------------------------------
//Form events
/*console.log(inputName);*/

const inputName = document.querySelector('input[type="text"]');
const select = document.querySelector("select");
const form = document.querySelector("form");
let pseudo = "";
let language = "";
inputName.addEventListener("input", (e) => {
    pseudo = e.target.value;
    /*console.log(pseudo);*/
});

select.addEventListener("input", (e) => {
    language = (e.target.value);
});

form.addEventListener("submit", (e) => {
    e.preventDefault();/*annule le changement de page*/
    /*console.log(cgv.checked);*/

    if (cgv.checked) {
        document.querySelector('form > div').innerHTML = `
        <h3>Pseudo : ${pseudo}</h3>
        <h4>Language préféré : ${language}</h4>
        `;

        //affiche le contenu des variables
    }
    else {
        alert("Veuillez accepter les C.G.V");
    }

});
//----------------------------------------------------
//load event
window.addEventListener("load", () => {
    console.log("Document Chargé ! ");
});
//---------------------------------------------------
//forEach  pour chacun d'eux...
/*const boxes = document.querySelectorAll(".box");

console.log(boxes);
boxes.forEach((box) => {
    box.addEventListener("click", (e) => {
        e.target.style.transform = "scale(0.7)";
    })
    });*/
//----------------------------------------------------------
//addEventListener Vs Onclock
//document.body.onclick = /*function */(e) => {
//   console.log("Click !");
//};
//les methodes onclick on schroll on mouse s,ecrasent et ne se cummulent pas
//plusieurs onclick sur body juste le dernier sera prit en compte

//Bubbling =>fin(eventlistener est par defaut en mode
//bubbling)

//document.body.addEventListener("click", () => {
//   console.log("click 1 !");
//});

//Usecapture
//document.body.addEventListener("click", () => {
//   console.log("click 2 !");
//}, true);
//https://gomakethings.com/articles/


//-------------------------------------------------------
//Stop propagation
//questionContainer.addEventListener("click", (e) => {
//  e.stopPropagation();
//});
//removeEventListener
//----------------------------------------------------------
//BOM
//console.log(window.innerHeight);
//console.log(window.scrollY):

//window.open('http://google.com', "cours steph js", "height=800",
//"width=800");
/*redirection vers une popup par exemple google*/
/*window.close()   ds la console ferme le navigateur*/

//confirm
btn2.addEventListener("click", () => {
    confirm("Voulez vous vraiment vous trompoez ?");
})

let answer;

//prompt
btn1.addEventListener("click", () => {
    let answer = prompt("Entrez votre Nom !!!");

    questionContainer.innerHTML += "<h3>Bravo " + answer + "</h3>";
});

//timer compte a rebours
setTimeout(() => {
    questionContainer.style.borderRadius = "300px";
}, 2000);
//creer des boites a interval controlé
/*let interval = setInterval(() => {
    document.body.innerHTML += `
    <div class ='box'>
    <h2>Nouvelle Boîte !</h2>
    </div>
    `;
},1000);
//effacer toues les parties body cliquées

document.body.addEventListener("click", (e) => {

   e.target.remove();
    clearInterval(interval);
});*/

//location
//console.log(location.href);
//console.log(location.host);
//console.log(location.pathname);
//console.log(location.search);

/*window.onload = () => {
    location.href = "http://twitter.fr";
};*/

//video a 3.39.36


