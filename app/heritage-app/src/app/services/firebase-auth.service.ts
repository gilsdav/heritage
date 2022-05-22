import { Injectable } from '@angular/core';
import { AngularFireAuth } from '@angular/fire/compat/auth';
import firebase from 'firebase/compat/app';

@Injectable({
  providedIn: 'root'
})
export class FirebaseAuthService {

  constructor(public angularFire: AngularFireAuth) {}

  signInWithEmail(email: string, password: string): Promise<firebase.auth.UserCredential> {
    return this.angularFire.signInWithEmailAndPassword(email, password);
  }

}
