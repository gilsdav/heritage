import { Injectable, Optional, Provider } from '@angular/core';
import {
  HttpEvent, HttpInterceptor, HttpHandler, HttpRequest, HTTP_INTERCEPTORS
} from '@angular/common/http';
import { take, switchMap, tap } from 'rxjs/operators';
import { Auth, authState } from '@angular/fire/auth';
import { from, of } from 'rxjs';
import { traceUntilFirst } from '@angular/fire/performance';

@Injectable({
  providedIn: 'root'
})
export class AuthInterceptor implements HttpInterceptor {

  constructor(@Optional() private auth: Auth){}

  intercept(req: HttpRequest<any>, next: HttpHandler) {
    return authState(this.auth).pipe(
      // traceUntilFirst('auth'),
      take(1),
      tap(user => console.log('user: ', user)),
      switchMap(user => user ? from(user.getIdToken()) : of(null)),
      switchMap(token => {
        let request = req;
        if (token) {
          request = request.clone({
            setHeaders: {
              // eslint-disable-next-line @typescript-eslint/naming-convention
              Authorization: `Bearer ${token}`
            }
          });
        }
        return next.handle(request);
      }),
    );
  }
}

export const authInterceptorProvider: Provider = {
  provide: HTTP_INTERCEPTORS,
  useClass: AuthInterceptor,
  multi: true
};
