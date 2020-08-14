var firebaseConfig = {
    apiKey: "AIzaSyBmjLDJgvdzfkZ5uuFNLog2jr6CGzH4bWI",
    authDomain: "tutorfinder-86503.firebaseapp.com",
    databaseURL: "https://tutorfinder-86503.firebaseio.com",
    projectId: "tutorfinder-86503",
    storageBucket: "tutorfinder-86503.appspot.com",
    messagingSenderId: "359593985253",
    appId: "1:359593985253:web:9af543ae11011bed1d800c",
    measurementId: "G-3BJ5MTHP24"
  };
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);

//reference messages collection
var messagesRef = firebase.database().ref('messages');
//listen for form submit for login
document.getElementById('b11').addEventListener('click',submitformL);
//for registering
document.getElementById('b12').addEventListener('click',submitformR);
function submitformL(e)
{
    e.preventDefault();
    console.log(123);

    var userId = getInputval('UserIdL');
    var password = getInputval('passwordL');
    //console.log(userId);
    //console.log(password);
    saveMessage(userId,password);
}
function submitformR(e)
{
    e.preventDefault();
    //console.log(123);

    var userId = getInputval('UserIdR');
    var password = getInputval('passwordR');
    var email = getInputval('emailR');

    
}
//function to get form value
function getInputval(id)
{
    return document.getElementById(id).value;
}

function saveMessage(UserId,password)
{
    var newmessageref=messagesRef.push();
    newmessageref.set({
        UserId:UserId,password:password
    });

}