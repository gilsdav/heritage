import { Injectable, Optional } from '@angular/core';
import { Auth, authState } from '@angular/fire/auth';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';
import { Observable } from 'rxjs';
import { map, take, tap } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {

  constructor(@Optional() private auth: Auth, private router: Router) {}

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): Observable<boolean> | Promise<boolean> | boolean {
    return authState(this.auth).pipe(
      take(1),
      map(u => !!u),
      tap(logged => {
        this.router.navigate(['/login']);
      })
    );
  }
}
