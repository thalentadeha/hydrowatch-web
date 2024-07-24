// Import the functions you need from the SDKs you need
import { initializeApp } from "firebase/app";
import { getAnalytics } from "firebase/analytics";
// TODO: Add SDKs for Firebase products that you want to use
// https://firebase.google.com/docs/web/setup#available-libraries

// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
const firebaseConfig = {
  apiKey: "AIzaSyAPkDZg_G9LZG3BgS906t0frdDOlKAcfk8",
  authDomain: "hydrowatch-testing.firebaseapp.com",
  projectId: "hydrowatch-testing",
  storageBucket: "hydrowatch-testing.appspot.com",
  messagingSenderId: "193728248140",
  appId: "1:193728248140:web:dcf3f61a35ea7c59deb7e9",
  measurementId: "G-CME1973XZF"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);
const analytics = getAnalytics(app);
