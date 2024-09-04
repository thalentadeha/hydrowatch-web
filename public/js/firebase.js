import {
    initializeApp
} from "https://www.gstatic.com/firebasejs/10.13.1/firebase-app.js";

import {
    getAnalytics
} from "https://www.gstatic.com/firebasejs/10.13.1/firebase-analytics.js";

import {
    getToken,
    getMessaging
} from "https://www.gstatic.com/firebasejs/10.13.1/firebase-messaging.js";

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

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);

const messaging = getMessaging(app);

// Get FCM token
// async function requestPermission() {
//     try {
//         // Check current notification permission
//         if (Notification.permission === 'denied') {
//             console.log('Notifications have been blocked. Please enable notifications manually in your browser settings.');
//             // Show a message to the user
//             showPermissionBlockedMessage();
//             return;
//         }

//         // Request permission to display notifications
//         const permission = await Notification.requestPermission();
//         if (permission === 'granted') {
//             // Get the FCM token if permission is granted
//             const token = await getToken(messaging, { vapidKey: "BHcCqa0Uyr4JjZ9kCNSoZiU1fRnxbE2OE9XghEtDDEKsPx5XjOZ3gl7-087DfsDgpz1-3sUW8HzFDTpex5g08hI" });
//             if (token) {
//                 console.log('FCM Token:', token);
//                 saveToken(token);
//             } else {
//                 console.log('No registration token available.');
//             }
//         } else if (permission === 'denied') {
//             console.log('Notifications permission denied. Please enable notifications manually in your browser settings.');
//             showPermissionBlockedMessage();
//         }
//     } catch (error) {
//         console.error('Error getting FCM token:', error);
//     }
// }

// requestPermission();

getToken(messaging, {
    vapidKey: 'BHcCqa0Uyr4JjZ9kCNSoZiU1fRnxbE2OE9XghEtDDEKsPx5XjOZ3gl7-087DfsDgpz1-3sUW8HzFDTpex5g08hI'
})
    .then((currentToken) => {
        if (currentToken) {
            console.log('FCM token:', currentToken);
            saveToken(currentToken)
        } else {
            console.log('No registration token available.');
        }
    })
    .catch((err) => {
        console.error('An error occurred while retrieving token. ', err);
    });

function saveToken(currentToken) {
    fetch('saveDeviceToken_POST', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            deviceToken: currentToken
        })
    })
        .then(response => response.json())
        .then(data => console.log('Token sent to server:', data))
        .catch(error => console.error('Error sending token to server:', error));
}

function showPermissionBlockedMessage() {
    const messageElement = document.createElement('div');
    messageElement.innerHTML = `
        <p>Notifications have been blocked. To receive updates, please enable notifications for this site in your browser settings.</p>
        <p><a href="https://support.google.com/chrome/answer/9568636?hl=en" target="_blank">How to enable notifications in Chrome</a></p>
        <p><a href="https://support.mozilla.org/en-US/kb/notifications-firefox" target="_blank">How to enable notifications in Firefox</a></p>
        <p><a href="https://support.microsoft.com/en-us/help/4520914/windows-10-enable-or-disable-notifications" target="_blank">How to enable notifications in Edge</a></p>
    `;
    messageElement.style.position = 'fixed';
    messageElement.style.bottom = '0';
    messageElement.style.left = '0';
    messageElement.style.right = '0';
    messageElement.style.backgroundColor = '#f8d7da';
    messageElement.style.color = '#721c24';
    messageElement.style.padding = '10px';
    messageElement.style.zIndex = '1000';
    document.body.appendChild(messageElement);
}
