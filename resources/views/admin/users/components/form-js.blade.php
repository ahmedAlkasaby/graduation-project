{{-- Roles only belong to the types in data-role-types (the admin type), so the field hides for the others. --}}
<script type="text/javascript">
    $(function () {
        var rolesWrap = $('#user-roles-wrap');
        var roleTypes = rolesWrap.data('role-types') || [];

        function toggleRoles() {
            rolesWrap.toggle(roleTypes.indexOf($('#user-type').val()) !== -1);
        }

        $('body').on('change', '#user-type', toggleRoles);
        toggleRoles();
    });
</script>
