let bCheckEnabled = true;
let bFinishCheck = false;

let img;
let imgArray = [];
let s = 1;

let intervalCounter = 0;

let myInterval;

if (document.querySelector(".game-infos") != null) {

    if(Cookies.get('can-play-game') == undefined) {
        myInterval = setInterval(loadImage, 1);
    } else {
        cantPlay();
    }

}

class Card {

    constructor(name, image) {
        this.name = name;
        this.image = image;


        this.cover = "/wp-content/themes/tlth/assets/images/cards/Noel-Cover.png"


        this.currentImage = this.cover;
    }

    flip() {

        if (this.currentImage === this.cover) {
            this.currentImage = this.image;
        } else {
            this.currentImage = this.cover;
        }
    }

}

let cardArray = [];
let chances = 3;
let chancesLeft = chances;

const reset = new CustomEvent("reset", {bubbles: false});

function loadImage() {

    // Trouve les images dans le dossier approprié et mets-les dans un array
    if (bFinishCheck) {
        clearInterval(myInterval);
        return;
    }

    if (bCheckEnabled) {

        bCheckEnabled = false;

        img = new Image();
        img.onload = fExists;
        img.onerror = fDoesntExist;

        let index = s;


        img.src = '/wp-content/themes/tlth/assets/images/cards/rubik/Noel-' + index + '.png';

    }

}

function fExists() {
    imgArray.push(img);
    s++;
    if(s > 10) {
        bCheckEnabled = false;
        fDoesntExist();
    } else {
        bCheckEnabled = true;
    }
}

function fDoesntExist() {
    bFinishCheck = true;

    if(intervalCounter < 1) {
        intervalCounter++;
        bCheckEnabled = true;
        bFinishCheck = false;
        s = 1;
        myInterval = setInterval(loadImage, 1);
    } else {
        imgArray = shuffle(imgArray);
        createCards();
    }

}

function shuffle(array) {
    let arrayLength = array.length;
    let shuffledArray = [];

    while (arrayLength > 0) {

        // Prends une carte aléatoire, mets la dans le nouvel array et enlève la de l'ancien, recommence jusqu'à temps qu'elles soient tous shuffled
        let randomNb = Math.floor(Math.random() * arrayLength);
        shuffledArray.push(array[randomNb]);
        array.splice(randomNb, 1);

        arrayLength = array.length;
    }

    return shuffledArray;

}

function createCards() {

    // Crée des Cards avec les images de l'array créé plus haut
    for (let j = 0; j < imgArray.length; j++) {

        let card = new Card(j+1, imgArray[j].src);
        cardArray.push(card);
    }

    initGame();

}

function initGame() {

    let gameDiv = document.querySelector(".game-content");

    // Créé une carte dans le DOM et attribue leur les images
    for (let j = 0; j < cardArray.length; j++) {

        let currentCard = cardArray[j];

        let theCard = document.createElement("img");
        let theCardDiv = document.createElement("div");

        theCard.currentCard = currentCard;

        theCard.src = theCard.currentCard.currentImage;
        theCardDiv.classList.add("card-holder");

        // Mets la carte dans sa propre div et mets cette div dans le container du jeu
        theCardDiv.appendChild(theCard);
        gameDiv.appendChild(theCardDiv);

        // On clique sur la carte et elle flip
        theCard.addEventListener("click", canFlipCard);

        theCard.addEventListener("reset", () => {

            // Flip la carte et actualise l'image
            theCard.currentCard.flip();
            theCard.src = theCard.currentCard.currentImage;
            theCard.classList.remove("flipped");

            // Permet à la carte de se refaire flipper
            theCard.addEventListener("click", canFlipCard);

        });

    }


}

function canFlipCard(e) {

    let theCard = e.currentTarget;

    if(chancesLeft > 0) {
        // Flip la carte et actualise l'image
        theCard.currentCard.flip();
        theCard.src = theCard.currentCard.currentImage;
        theCard.classList.add("flipped");

        // Une fois la carte flippée, on ne veut pas pouvoir la "déflipper"
        theCard.removeEventListener("click", canFlipCard);

        // Attends 0.1sec et compare les cartes pour une paire
        setTimeout(checkPairs, 100);
    }

}

function checkPairs() {
    // Trouve toutes les cartes qui sont flippées
    let flippedCards = document.querySelectorAll(".flipped");

    //Si au moins 2 cartes sont flippées
    if(flippedCards.length >= 2) {

        // Si les deux cartes sont pareilles
        if(flippedCards[0].src === flippedCards[1].src) {
            //Bonne paire
            alert("Bravo! Ces cartes sont une paire!");

            /*Changement de page pour prix gagnant*/
            Cookies.set('can-play-game', 'false', {expires: 300, });
            submitPrize(1); // 1 veut dire qu'on gagne un prix

        } else {
            //Mauvaise paire
            chancesLeft--;

            if (chancesLeft > 0) {
                alert("Ces cartes ne sont pas une paire! " + chancesLeft + " chances restantes");
            } else {
                alert("Ces cartes ne sont pas une paire! Désolé, c'était votre dernière chance!");

                /*Changement de page pour le prix perdant*/
                Cookies.set('can-play-game', 'false', {expires: 300, });
                submitPrize(0); // 0 veut dire qu'on a le prix par défaut
            }

            // "Déflippe" les cartes
            for (let j = 0; j < flippedCards.length; j++) {
                flippedCards[j].classList.remove("flipped");
                flippedCards[j].dispatchEvent(reset);
            }


        }

    }

}

function submitPrize(value) {

    document.getElementById("isWinner").value = value;
    document.getElementById("prizeForm").submit();

}

function cantPlay() {

    let main = document.querySelector("#main");
    let game = document.querySelector("#game");
    let gameInfos = document.querySelector(".game-infos");
    let message = document.querySelector(".game-notice");
    main.style.marginBottom = "100px";
    game.style.height = "0";
    gameInfos.style.display = "none";
    message.style.display = "initial";

}

// Checking for Retina Devices
function isRetina() {
    let query = '(-webkit-min-device-pixel-ratio: 1.5),\
    (min--moz-device-pixel-ratio: 1.5),\
    (-o-min-device-pixel-ratio: 3/2),\
    (min-device-pixel-ratio: 1.5),\
    (min-resolution: 144dpi),\
    (min-resolution: 1.5dppx)';

    if (window.devicePixelRatio > 1 || (window.matchMedia && window.matchMedia(query).matches)) {
        return true;
    }
    return false;
}