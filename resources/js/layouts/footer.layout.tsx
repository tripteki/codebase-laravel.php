import { FC, } from "react";
import { useTranslation, } from "react-i18next";

const FooterLayout: FC = () =>
{
    const { t, }: { t: Function; } = useTranslation ();

    return (
        <footer className="border-t mt-auto">
            <div className="container mx-auto px-4 py-4">
                <p className="text-center text-sm text-muted-foreground">
                    © {new Date ().getFullYear ()} {t ("common.all_rights_reserved")}
                </p>
            </div>
        </footer>
    );
};

export default FooterLayout;
