// Import and configure the Firebase SDK
// importScripts('https://www.gstatic.com/firebasejs/10.13.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/10.13.1/firebase-messaging.js');


// Your web app's Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyAPkDZg_G9LZG3BgS906t0frdDOlKAcfk8",
    authDomain: "hydrowatch-testing.firebaseapp.com",
    databaseURL: "https://hydrowatch-testing-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "hydrowatch-testing",
    storageBucket: "hydrowatch-testing.appspot.com",
    messagingSenderId: "193728248140",
    appId: "1:193728248140:web:dcf3f61a35ea7c59deb7e9",
    measurementId: "G-CME1973XZF"
};

// Initialize Firebase in the service worker
firebase.initializeApp(firebaseConfig);

// Retrieve an instance of Firebase Messaging
const messaging = firebase.messaging();

// Handle background messages
// messaging.onBackgroundMessage((payload) => {
//     // console.log('Received background message:', payload);

//     // // Extract title and body from payload.notification
//     // const { title, body, icon } = payload.notification;

//     // // Customize the notification
//     // const notificationTitle = title || 'Time to Drink Water!';
//     // const notificationOptions = {
//     //     body: body || 'You haven\'t had a drink in the last hour. Stay hydrated!',
//     //     icon: icon || 'img/logo.png',
//     // };

//     // // Show the notification
//     // self.registration.showNotification(notificationTitle, notificationOptions);

//     console.log("Received background message ", payload);

//     const notificationTitle = payload.notification.title;
//     const notificationOptions = {
//         body: payload.notification.body,
//     };

//     self.registration.showNotification(notificationTitle, notificationOptions);
// });

messaging.setBackgroundMessageHandler(function(payload) {
  console.log(
    "[firebase-messaging-sw.js] Received background message ",
    payload,
  );
  /* Customize notification here */
  const notificationTitle = "Background Message Title";
  const notificationOptions = {
    body: "Background Message body.",
    icon: "/itwonders-web-logo.png",
  };

  return self.registration.showNotification(
    notificationTitle,
    notificationOptions,
  );
});
