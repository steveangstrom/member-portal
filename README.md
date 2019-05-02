# member-portal
2019 - refactoring so that it can be used with Woocommerce, so a user can purchase a membership as a woo products
Portal Options sets the various pages for things such as the Sign in Page, Register Page, the "user Page" (their home) the menu identifier for the menu item and the product ID for membership.

create a product in Woo. set the variations of 1,6,12  these will be the durations.
get the ID of that product and put it into the options of the portal. the product ID for membership.

when a user tries to visit a portal page, and they are logged in, the page will check their Woo order history to see if they have bought a product with the id which matches the Membership product. it will then check the variation / duration and purchase date to see when it expires/expired.
