export default function checkRole(value) {
	if (value && value instanceof Array && value.length > 0) {
	    const roles = this.$store.getters && this.$store.getters.user.roles;

	    const requiredRoles = value;

		if(roles.includes('super_admin'))
			return true;

        const hasRole = roles.some(role => {
            return requiredRoles.includes(role);
        });

	    return hasRole;
	} else {
	  console.error(`Need roles! Like v-role="['admin','editor']"`);
	  return false;
	}
}