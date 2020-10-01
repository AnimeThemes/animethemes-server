import {StyledBody, StyledSidebar, StyledSidebarView} from "./sidebarView.styled";

export default function SidebarView({ sidebar, children }) {
    return (
       <StyledSidebarView>
           <StyledSidebar>
               {sidebar}
           </StyledSidebar>
           <StyledBody>
               {children}
           </StyledBody>
       </StyledSidebarView>
    );
}
