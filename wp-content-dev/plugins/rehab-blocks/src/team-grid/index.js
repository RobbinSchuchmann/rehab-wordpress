import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, Notice } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const filters = a.filters || [];
		return (
			<>
				<InspectorControls>
					<PanelBody title="Section" initialOpen>
						<SelectControl
							label="Background"
							value={ a.background }
							options={ [ { label: 'Cream', value: 'cream' }, { label: 'White', value: 'white' }, { label: 'Sage mist', value: 'sage-mist' } ] }
							onChange={ set( 'background' ) }
						/>
						<Notice status="info" isDismissible={ false }>
							Team members are managed under <strong>Team</strong> in the admin sidebar. This grid automatically shows every member flagged <em>“Feature on team page”</em>, ordered by their sort order, with discipline filter chips built from the roster.
						</Notice>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-team-grid rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<div className="rehab-team-grid__head">
								<RichText tagName="span" className="rehab-team-grid__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
								<RichText tagName="h2" className="rehab-team-grid__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
								<RichText tagName="p" className="rehab-team-grid__lede" value={ a.lede } onChange={ set( 'lede' ) } placeholder="Lede…" allowedFormats={ [] } />
							</div>
							<div className="rehab-team-grid__filter">
								{ filters.map( ( f, i ) => <button key={ i } type="button" className={ i === 0 ? 'on' : '' }>{ f.label }</button> ) }
							</div>
							<div className="rehab-team-grid__editor-note">
								Team members render here on the front end, pulled live from the <strong>Team</strong> records flagged “Feature on team page”. Edit a member (photo, role, bio, discipline, order, feature toggle) under Team in the sidebar.
							</div>
						</div>
					</section>
				</div>
			</>
		);
	},
	save: () => null,
} );
