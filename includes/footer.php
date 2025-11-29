    </div> 

    
    <footer class="footer">
        <div class="footer-content">

            <div class="footer-left">
                <div class="footer-logo">
                    <span class="footer-logo-text">BookReserve</span>
                    <img src="imgs/logo.png" alt="Logo" class="footer-logo-img">
                </div>
                <p class="footer-tagline">reserving books with ease.</p>
            </div>

            <div class="footer-right">
                <p><strong>Contact Us</strong></p>
                <p>Email: support@bookreserve.ie</p>
                <p>Phone: +353 83 2081803</p>
                <p>Address: 23 The Burnaby, Greystones, Wicklow</p>
            </div>

        </div>

        <div class="footer-bottom">
            © 2025 BookReserve — All rights reserved.
        </div>
    </footer>

</div> <!-- end page-wrapper -->

<style>
    .footer {
        background: #14161f;
        padding: 18px 28px; 
        color: #cbd0e0;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 25px; 
    }

    /* Left side */
    .footer-logo {
        display: flex;
        align-items: center;
        gap: 6px; 
    }

    .footer-logo-text {
        font-size: 18px; 
        font-weight: bold;
        color: #9CA7D1;
    }

    .footer-logo-img {
        height: 22px; 
        width: auto;
        margin-top: -2px;
        object-fit: contain;
    }

    .footer-tagline {
        margin-top: 6px; 
        font-size: 11px; 
        color: #9fa6bc;
    }

    /* Right side */
    .footer-right p {
        margin: 3px 0;
        font-size: 11px; /
        color: #cbd0e0;
    }

    .footer-right strong {
        font-size: 12px; 
        color: #e4e8f2;
    }

    /* Bottom bar */
    .footer-bottom {
        text-align: center;
        margin-top: 15px; /
        padding-top: 10px; 
        border-top: 1px solid #2c3145;
        font-size: 11px; 
        color: #78809a;
    }

    
    @media (max-width: 600px) {
        .footer-content {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
    }
</style>

</body>
</html>
