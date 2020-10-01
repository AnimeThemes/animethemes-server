import {StyledIcon, StyledIconText} from "./iconText.styled";

export default function IconText({ icon, iconProps, warning, children, ...props }) {
    return (
        <StyledIconText {...props}>
            {icon && (
                <StyledIcon icon={icon} fixedWidth warning={warning} {...iconProps}/>
            )}
            {children}
        </StyledIconText>
    );
}
