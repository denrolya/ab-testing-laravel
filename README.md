## Backend Developer Homework

Congratulations on getting to this part of our interview process!

Here you can find a basic Laravel project configured with some extra features.

Your task is to design and implement a basic [A/B testing](https://en.wikipedia.org/wiki/A/B_testing) system.

This application already starts a basic session in the database for visitors and is capable of adding events to these
sessions.

Requirements:

- An A/B test has a name and 2 or more variants
- Variants have a name and a targeting ratio. The system decides which variant to select for a given A/B test based on
  the targeting ratios (compared to each other)
- Example: variant A (targeting ratio: 1), variant B (targeting ratio: 2) - in this case, variant B is 2 times more
  likely to be selected than variant A
- An A/B test can be started and stopped, after stopping, it cannot be restarted
- At the same time, more A/B tests can run simultaneously
- When an A/B test is running:
    - new sessions should be assigned to one of the variants of the A/B test
    - the site should behave according to the variant selected
    - the site should behave consistently in a given session, i.e. it should not behave according to variant A at first
      and then according to variant B later

After implementing the above system, create an A/B test (you can use a migration to start it) and demonstrate the usage
of the system by changing some behaviour of the site (that is visible to the visitors) based on the A/B test variant.

# Solution
I aimed for:

* Completely satisfy the requirements
* Solution should follow **clean code**, **SOLID** principles, **KISS**, **DRY** and **Laravel best practices**.

Considering that said, and the fact that it was ***mentioned 2h time*** for implementation:

* I didn't want to overcomplicate or overengineer the solution.
* No additional/cool features/conveniences implemented, strictly following the requirements.
* I didn't focus on fancy visual representation of the solution.
* I didn't focus on implementing robust abstraction layers, but rather on the basic functionality as I would in a real project.
* I didn't focus on making number of commits with clear messages as I would in a real project.
_____

## Workflow

### Implemented Workflow

1. When a session starts, the system checks if a variant for the A/B test has already been assigned to the session.
2. If not, it assigns a variant based on the targeting ratio of the variants.
3. The site behavior changes according to the assigned variant for the duration of the session
4. After the test is no longer needed we can call ```AB::stopTest($test)``` function to stop it. This function does stop the test and removes it from current user's session ***HOWEVER*** it is also required to implement a middleware that would assert that the test is still running and that the user is assigned to a variant. This is not implemented in the current version as it was not required by the task.

The original workflow has the advantage of ensuring a variant is assigned at the start of a session, which can be beneficial if the A/B test affects the initial page load, providing a consistent site behavior from the start. However, it might assign a variant even if it's not needed, potentially wasting resources if the A/B test doesn't affect all users or all pages. Additionally, it requires checking for an assigned variant at the start of every session, which could add overhead.

### Possible alternative
1. Do nothing when a session starts.
2. As soon as we need to assert a variant option is selected:
   1. Check if specified variant for specified test is selected
   2. If test is not present in the session: we assign a variant based on the targeting ratio of the variants.

On the other hand, the alternative workflow is more efficient as it only assigns a variant when needed, saving resources if the A/B test doesn't affect all users or all pages. It also avoids unnecessary checks at the start of every session, reducing overhead. However, the site behavior might not be consistent from the start of the session if the A/B test affects the initial page load. Furthermore, it could potentially delay the assignment of a variant until a user interacts with a part of the site affected by the A/B test. Practically, very roughly without getting into optimizations, that would mean smth like this:
```php
public static function isVariantSelected(string $testName, string $variantName): bool
{
    $sessionKey = self::SESSION_KEY.'.'.$testName;
    if (session()->has($sessionKey)) {
        return session($sessionKey) === $variantName;
    }
       
   // TODO: Find this test and check if it's running
   // TODO: use $this->handleABTestVariantSelection($newTest);
}
```
____

I picked the first approach mainly because of it's simplicity. Since there are many ways to improve the system I decided to implement a simple but scalable solution. As you can see from suggested alternative all the methods are already implemented, basically only thing needed to do is to get rid of `AssignABTestsVariants` middleware.
_____

## Run and Test

Command to run: ```artisan migrate && artisan db:seed && artisan serve```


I've created a seeder for 2 very basic 2 A/B tests with tailwind colors as variants:

 - `background_color` - changes the background color of the site: `lime` or `slate`
 - `font_color` - changes the title font color of the site: `indigo` or `teal`
 
Visit the site and see the changes made to background and title font color based on the A/B test variant.

## Further Improvements
There are improvements that I would consider to the current implementation:

* Complete stopping of the A/B test for all the users
* Automated testing
* Logging & Reporting
* Optimize for large data sets
* Use Redis or Memcached
* Better cache management
* Proper Exception handling
* Frontend integration
* Ability to specify duration for A/B tests
* Console commands for managing A/B tests
* Blade directives for A/B testing
