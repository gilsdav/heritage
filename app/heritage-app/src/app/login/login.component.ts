import { Component, OnInit, OnDestroy, Optional } from '@angular/core';
import {
  Auth,
  authState,
  signOut,
  User,
  signInWithEmailAndPassword,
  createUserWithEmailAndPassword,
  sendPasswordResetEmail
} from '@angular/fire/auth';
import { EMPTY, from, Observable, Subscription } from 'rxjs';
import { map, switchMap, tap } from 'rxjs/operators';
import { traceUntilFirst } from '@angular/fire/performance';
import { ActivatedRoute } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit, OnDestroy {

  public readonly user: Observable<User | null> = EMPTY;
  showLoginButton = false;
  showLogoutButton = false;
  public readonly form: FormGroup | null = null;

  private readonly userDisposable: Subscription|undefined;

  constructor(@Optional() private auth: Auth, private route: ActivatedRoute, private http: HttpClient) {
    this.form = new FormGroup({
      email: new FormControl('', [ Validators.required ]),
      password: new FormControl('', [ Validators.required ])
    });
    if (auth) {
      this.user = authState(this.auth);
      this.userDisposable = authState(this.auth).pipe(
        traceUntilFirst('auth'),
        // tap(u => {
        //   updateProfile(u, { displayName: 'David Gilson' });
        // }),
      ).subscribe(user => {
        const isLoggedIn = !!user;
        this.showLoginButton = !isLoggedIn;
        this.showLogoutButton = isLoggedIn;
        if (user) {
          user.getIdToken().then(t => console.log(t));
        }
      });
    }
  }

  ngOnInit(): void {
    this.route.queryParams.subscribe(params => {
      if (params.email) {
        this.form.get('email').setValue(params.email);
      }
    });
    this.http.get('http://localhost:8000/api/users').subscribe(users => {
      console.log(users);
    });
  }

  ngOnDestroy(): void {
    if (this.userDisposable) {
      this.userDisposable.unsubscribe();
    }
  }

  login() {
    if (this.form.valid) {
      from(signInWithEmailAndPassword(this.auth, this.form.get('email').value, this.form.get('password').value)).subscribe(success => {
        console.log(success);
      }, error => {
        // auth/wrong-password, auth/user-not-found
        console.log(error?.code === 'auth/user-not-found' || error?.code === 'auth/wrong-password');
      });
    }
  }


  logout() {
    signOut(this.auth);
  }

  signup() {
    from(createUserWithEmailAndPassword(this.auth,  'davgilson@gmail.com', 'testtest')).pipe(
      switchMap(() => from(sendPasswordResetEmail(this.auth, 'davgilson@gmail.com', {
        url: 'http://localhost:4200/login?email=davgilson@gmail.com'
       })))
    ).subscribe(() => {
      console.log('created');
    });
  }

}
