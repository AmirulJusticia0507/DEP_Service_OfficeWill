```markdown
# Development & Coding Guidelines - DEP Service

Dokumen ini berisi standar pengembangan, arsitektur, dan aturan logika bisnis untuk aplikasi **DEP Service**.

---

## 📁 Stuktur Proyek Laravel

Gunakan pendekatan **Action/Service Pattern** agar Controller tetap tipis (*Lean Controllers*):

```text
app/
├── Actions/
│   ├── Auth/
│   │   ├── HandlePasswordErrorAction.php
│   │   └── ReissuePasswordAction.php
│   ├── Course/
│   │   ├── AssignCourseToEmployeesAction.php
│   │   └── EvaluateTestResultAction.php
│   └── Employee/
│       └── FilterEmployeeByScopeAction.php
├── Enums/
│   ├── AuthorityScopeEnum.php      # AFFILIATION_ONLY, BELOW, ALL
│   ├── OrganizationTypeEnum.php  # MAIN_STORE (1), FC_STORE (2)
│   └── TodoTypeEnum.php           # QUESTIONNAIRE, REPORT, TEST
├── Models/
│   ├── Company.php
│   ├── Affiliation.php
│   ├── Job.php
│   ├── Employee.php
│   ├── EmployeeAffiliation.php
│   ├── CourseCategory.php
│   ├── CourseCategoryDetail.php
│   ├── Course.php
│   ├── CourseMaterial.php
│   ├── CourseTodo.php
│   ├── CourseEnrollment.php
│   └── CourseTodoResponse.php
├── Services/
│   └── ScopeFilterService.php
└── Mail/
    ├── AccountRegisteredMail.php
    ├── PasswordReissuedMail.php
    ├── PasswordChangedMail.php
    ├── CourseAssignedMail.php
    └── CourseCancelledMail.php