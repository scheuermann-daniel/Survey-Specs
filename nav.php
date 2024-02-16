<!-- Navigation section with an unordered list -->
<nav>
    <ul class="ulNav">

        <!-- Home link with conditional 'activePage' class -->
        <li class="liNav">
            <a href="index.php" class="aNav <?php if (PATH_PARTS['filename'] == "index") { print 'activePage'; } ?>">
                Home
            </a>
        </li>

        <!-- Survey link with conditional 'activePage' class -->
        <li class="liNav">
            <a href="survey.php" class="aNav <?php if (PATH_PARTS['filename'] == "survey") { print 'activePage'; } ?>">
                Survey
            </a>
        </li>

        <!-- Analysis link with conditional 'activePage' class -->
        <li class="liNav">
            <a href="analysis.php" class="aNav <?php if (PATH_PARTS['filename'] == "analysis") { print 'activePage'; } ?>">
                Specs
            </a>
        </li>

    </ul>
</nav>
