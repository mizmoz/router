


v0.5.0

- Change Dispatcher to throw RouteNotFoundException instead of returning 
- Fix bug in dispatcher where $request->getUri() would return an object rather than the path

v0.4.1

- Fix Route docblock comments

v0.4.0

- Add the router results object to the ServerRequest object instead of passing it the the route method separately

v0.3.0

- Change router to use ServerRequestInterface rather than RequestInterface

v0.2.0

- Add wildcard route matching like /app/*

v0.1.0

- Basic router

